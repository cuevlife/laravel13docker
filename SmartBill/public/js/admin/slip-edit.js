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
    const originalData = config.originalData || {};
    const columns = (config.columns || []).map(c => ({ key: c.key, label: c.label }));
    
    return {
        viewMode: 'ui', 
        showImage: false, 
        saving: false,
        originalData, 
        columns,
        jsonContent: JSON.stringify(originalData, null, 4),
        fields: columns.reduce((a, c) => { a[c.key] = originalData[c.key] ?? ''; return a; }, {}),
        items: Array.isArray(originalData.items) ? originalData.items.map((i, idx) => ({ uid: Date.now()+idx, name: i.name||'', price: i.price||'' })) : [],
        showItems: true,
        
        get mathMismatch() {
            if (!this.showItems || this.items.length === 0) return false;
            const sum = this.items.reduce((acc, curr) => acc + (parseFloat(curr.price) || 0), 0);
            
            const totalKey = Object.keys(this.fields).find(k => k.toLowerCase().includes('total') || k.toLowerCase().includes('amount'));
            if (!totalKey) return false;
            
            const declaredTotal = parseFloat(this.fields[totalKey]) || 0;
            if (declaredTotal > 0 && Math.abs(sum - declaredTotal) > 0.1) {
                return `Warning: Items calculate to ฿${sum.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} but declared total is ฿${declaredTotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`;
            }
            return false;
        },
        
        init() { 
            if (typeof lucide !== 'undefined') {
                lucide.createIcons(); 
            }
        },
        
        switchMode(mode) {
            if (mode === 'json') { 
                this.jsonContent = JSON.stringify(this.getCombined(), null, 4); 
            } else { 
                try { 
                    this.syncFrom(JSON.parse(this.jsonContent)); 
                } catch(e) { 
                    return Toast.fire({icon:'error', title:'Invalid JSON'}); 
                } 
            }
            this.viewMode = mode; 
            if (typeof lucide !== 'undefined') {
                this.$nextTick(() => lucide.createIcons());
            }
        },
        
        getCombined() {
            const data = {...this.originalData, ...this.fields};
            if (this.showItems) data.items = this.items.map(i => ({ name: i.name, price: i.price }));
            return data;
        },
        
        syncFrom(data) {
            this.originalData = data;
            this.columns.forEach(c => this.fields[c.key] = data[c.key] ?? '');
            if (Array.isArray(data.items)) {
                this.items = data.items.map((i, idx) => ({ uid: Date.now()+idx, name: i.name||'', price: i.price||'' }));
            } else {
                this.items = [];
            }
        },
        
        addItem() { 
            this.items.push({ uid: Date.now(), name: '', price: '' }); 
            if (typeof lucide !== 'undefined') {
                this.$nextTick(() => lucide.createIcons()); 
            }
        },
        
        handleEnterPress(index) {
            if (index === this.items.length - 1) {
                this.addItem();
            }
            this.$nextTick(() => {
                const nextInput = document.getElementById('item-name-' + (index + 1));
                if (nextInput) {
                    nextInput.focus();
                }
            });
        },
        
        removeItem(idx) { 
            this.items.splice(idx, 1); 
        },
        
        prettifyJson() { 
            try { 
                this.jsonContent = JSON.stringify(JSON.parse(this.jsonContent), null, 4); 
            } catch(e) {} 
        },
        
        async save() {
            this.saving = true;
            try {
                const data = this.viewMode === 'json' ? JSON.parse(this.jsonContent) : this.getCombined();
                const res = await fetch(config.updateRoute, {
                    method: 'POST', 
                    body: JSON.stringify({ data, _token: config.csrfToken }),
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
                });
                
                if (!res.ok) throw new Error('Update failed');
                
                Toast.fire({ icon: 'success', title: 'Registry Synced' });
                setTimeout(() => window.location.href = config.indexRoute, 900);
            } catch (e) { 
                Toast.fire({ icon: 'error', title: e.message || 'An error occurred' }); 
            } finally { 
                this.saving = false; 
            }
        }
    };
}
