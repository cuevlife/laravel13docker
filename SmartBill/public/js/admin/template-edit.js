/**
 * SmartBill - Template Editor Intelligence
 * Script for handling the extraction rules builder and JSON mode.
 */

const Toast = Swal.mixin({ 
    toast: true, 
    position: 'top-end', 
    showConfirmButton: false, 
    timer: 3000 
});

window.templateEditor = function(config) {
    const initialFields = (config.promptFields || []).map(f => ({ key: f.key, label: f.label || f.key, type: f.type || 'text' }));
    
    return {
        viewMode: 'ui', 
        jsonContent: JSON.stringify(initialFields, null, 4),
        form: { 
            merchant_id: config.merchantId,
            name: config.merchantName, 
            main_instruction: config.mainInstruction 
        },
        fields: initialFields, 
        saving: false, 
        suggesting: false,
        
        init() { 
            if (typeof lucide !== 'undefined') {
                lucide.createIcons(); 
            }
        },
        
        switchMode(mode) {
            if (mode === 'json') { 
                this.jsonContent = JSON.stringify(this.fields, null, 4); 
            } else {
                try { 
                    const parsed = JSON.parse(this.jsonContent); 
                    this.fields = parsed.map(f => ({ 
                        key: f.key || this.generateKey(f.label || ''), 
                        label: f.label || f.key || 'Header', 
                        type: f.type || 'text' 
                    })); 
                } catch (e) { 
                    Toast.fire({ icon: 'error', title: 'Invalid JSON' }); 
                    return; 
                }
            }
            this.viewMode = mode; 
            if (typeof lucide !== 'undefined') {
                this.$nextTick(() => lucide.createIcons());
            }
        },
        
        generateKey(label) { 
            return label.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, ''); 
        },
        
        syncKey(field) { 
            field.key = this.generateKey(field.label); 
        },
        
        addField() { 
            this.fields.push({ key: '', label: '', type: 'text' }); 
            if (typeof lucide !== 'undefined') {
                this.$nextTick(() => lucide.createIcons()); 
            }
        },
        
        removeField(index) { 
            this.fields.splice(index, 1); 
        },
        
        prettifyJson() { 
            try { 
                this.jsonContent = JSON.stringify(JSON.parse(this.jsonContent), null, 4); 
            } catch (e) { 
                Toast.fire({ icon: 'error', title: 'Malformed JSON' }); 
            } 
        },
        
        async suggestFromImage(event) {
            const file = event.target.files[0]; 
            if (!file) return;
            
            this.suggesting = true; 
            const fd = new FormData(); 
            fd.append('image', file); 
            fd.append('_token', config.csrfToken);
            
            try {
                const res = await fetch(config.suggestRoute, { 
                    method: 'POST', 
                    body: fd 
                });
                const data = await res.json(); 
                if (!res.ok) throw new Error(data.message || 'AI failed');
                
                this.fields = data.ai_fields.map(f => ({ key: f.key, label: f.label, type: f.type }));
                this.jsonContent = JSON.stringify(this.fields, null, 4); 
                Toast.fire({ icon: 'success', title: 'Headers Detected' });
            } catch (e) { 
                Toast.fire({ icon: 'error', title: e.message }); 
            } finally { 
                this.suggesting = false; 
                event.target.value = ''; 
                if (typeof lucide !== 'undefined') {
                    this.$nextTick(() => lucide.createIcons()); 
                }
            }
        },
        
        async save() {
            this.saving = true;
            try {
                let finalFields = this.viewMode === 'json' ? JSON.parse(this.jsonContent) : this.fields;
                
                const fd = new FormData();
                fd.append('merchant_id', this.form.merchant_id);
                fd.append('name', this.form.name);
                fd.append('main_instruction', this.form.main_instruction || '');
                fd.append('ai_fields', JSON.stringify(finalFields));
                fd.append('export_layout', JSON.stringify(finalFields.map(f => ({ key: f.key, label: f.label, enabled: true }))));
                fd.append('_method', 'PATCH'); 
                fd.append('_token', config.csrfToken);
                
                const res = await fetch(config.updateRoute, { 
                    method: 'POST', 
                    body: fd, 
                    headers: { 'Accept': 'application/json' } 
                });
                
                if (!res.ok) throw new Error('Persistence failed');
                
                Toast.fire({ icon: 'success', title: 'Profile Updated' });
                setTimeout(() => window.location.href = config.indexRoute, 900);
            } catch (e) { 
                Toast.fire({ icon: 'error', title: e.message }); 
            } finally { 
                this.saving = false; 
            }
        }
    };
}
