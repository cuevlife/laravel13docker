/**
 * SmartBill - Slip Editor Intelligence
 * Script for handling the slip extraction, JSON/UI modes, and data saving.
 */

const Toast = Swal.mixin({ 
    toast: true, 
    position: 'top-end', 
    showConfirmButton: false, 
    timer: 3000 
});

window.slipEditor = function(config) {
    const initialOriginalData = config.originalData || {};
    const columns = (config.columns || [])
        .filter(c => c.key !== 'items')
        .map(c => ({ key: c.key, label: c.label }));
    
    return {
        viewMode: 'ui', 
        showImage: false, 
        saving: false,
        originalData: initialOriginalData, 
        columns,
        jsonContent: '',
        fields: {},
        items: [],
        showItems: true,
        
        // Re-scan state
        rescanModalOpen: false,
        isRescanning: false,
        rescanInstructions: '',

        init() {
            this.syncFromData(this.originalData);
            this.$watch('jsonContent', (val) => {
                if (this.viewMode === 'json') {
                    try {
                        const parsed = JSON.parse(val);
                        this.syncFromData(parsed, false);
                    } catch(e) {}
                }
            });
        },

        syncFromData(data, updateJson = true) {
            this.fields = this.columns.reduce((a, c) => { a[c.key] = data[c.key] ?? ''; return a; }, {});
            this.items = Array.isArray(data.items) ? data.items.map((i, idx) => {
                const parseName = (item) => item.name || item.item_name || item.item || item.description || item.desc || item.title || item.product || (typeof item === 'string' ? item : '');
                const parsePrice = (item) => {
                    let p = item.price ?? item.amount ?? item.total ?? item.total_price ?? item.value ?? item.cost;
                    return p !== undefined && p !== null ? String(p).replace(/[^0-9.-]+/g,"") : '';
                };
                return { uid: Date.now()+idx, name: parseName(i), price: parsePrice(i) };
            }) : [];
            if (updateJson) {
                this.jsonContent = JSON.stringify(data, null, 4);
            }
        },

        openRescanModal() {
            this.rescanInstructions = '';
            this.rescanModalOpen = true;
        },

        async performRescan() {
            this.isRescanning = true;
            try {
                const response = await fetch(config.rescanRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ instructions: this.rescanInstructions })
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Re-scan failed');

                this.originalData = result.data;
                this.syncFromData(this.originalData);
                this.rescanModalOpen = false;

                Toast.fire({
                    icon: 'success',
                    title: 'Re-scan successful'
                });
            } catch (e) {
                Toast.fire({
                    icon: 'error',
                    title: e.message || 'An error occurred during re-scan'
                });
            } finally {
                this.isRescanning = false;
            }
        },
        
        get mathMismatch() {
            if (!this.showItems || this.items.length === 0) return false;
            
            const sum = this.items.reduce((acc, curr) => {
                const cleanPrice = String(curr.price || '').replace(/[^0-9.-]+/g,"");
                return acc + (parseFloat(cleanPrice) || 0);
            }, 0);
            
            const totalKey = Object.keys(this.fields).find(k => k.toLowerCase().includes('total') || k.toLowerCase().includes('amount'));
            if (!totalKey) return false;
            
            const cleanDeclaredTotal = String(this.fields[totalKey] || '').replace(/[^0-9.-]+/g,"");
            const declaredTotal = parseFloat(cleanDeclaredTotal) || 0;
            
            if (Math.abs(sum - declaredTotal) > 0.1) {
                return `Calculated Sum (฿${sum.toFixed(2)}) doesn't match Declared Total (฿${declaredTotal.toFixed(2)})`;
            }
            return false;
        },

        switchMode(mode) {
            this.viewMode = mode;
        },

        prettifyJson() {
            try {
                const obj = JSON.parse(this.jsonContent);
                this.jsonContent = JSON.stringify(obj, null, 4);
            } catch(e) {
                Toast.fire({ icon: 'warning', title: 'Invalid JSON format' });
            }
        },

        async save() {
            if (this.saving) return;
            this.saving = true;
            
            try {
                let dataToSave = {};
                if (this.viewMode === 'json') {
                    dataToSave = JSON.parse(this.jsonContent);
                } else {
                    dataToSave = { ...this.fields, items: this.items };
                }

                const res = await fetch(config.updateRoute, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ data: dataToSave })
                });

                if (!res.ok) throw new Error('Failed to update');
                
                Toast.fire({ icon: 'success', title: 'Intelligence updated' });
                setTimeout(() => window.location.href = config.indexRoute, 900);
            } catch (e) { 
                Toast.fire({ icon: 'error', title: e.message || 'An error occurred' }); 
            } finally { 
                this.saving = false; 
            }
        }
    };
}
