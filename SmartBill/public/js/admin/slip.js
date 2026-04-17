const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2200,
    background: document.documentElement.classList.contains('dark') ? '#313338' : '#ffffff',
    color: document.documentElement.classList.contains('dark') ? '#f2f3f5' : '#1e1f22'
});

window.slipRegistry = function () {
    const config = window.initialSlipRegistryConfig || {};

    return {
        modalOpen: false,
        batchModalOpen: false,
        collectionModalOpen: false,
        detailOpen: false,
        loading: false,
        batchLoading: false,
        collectionLoading: false,
        processingStatus: 'Start Extraction',
        files: [],
        fileName: '',
        batchError: '',
        collectionError: '',
        activeSlip: null,
        selectedIds: [],
        bulkAction: 'mark_reviewed',
        bulkLabel: '',
        form: { template_id: 'auto', batch_id: '', batch_name: '', labels: '' },
        batchForm: { name: '', note: '' },
        collectionForm: { id: '', name: '', note: '' },

        init() {
            this.initDatePickers();
            this.syncSelectedSelections();
        },

        initDatePickers() {
            if (typeof flatpickr === 'undefined') return;

            const pad = (value) => String(value).padStart(2, '0');
            const defaultFormatter = flatpickr.formatDate;
            const defaultParser = flatpickr.parseDate;

            document.querySelectorAll('[data-slip-date]').forEach((input) => {
                flatpickr(input, {
                    locale: (flatpickr.l10ns && flatpickr.l10ns.th) ? flatpickr.l10ns.th : 'th',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    dateFormat: 'Y-m-d',
                    defaultDate: input.value || null,
                    allowInput: false,
                    formatDate(date, format) {
                        if (format === 'Y-m-d') {
                            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                        }

                        if (format === 'd/m/Y') {
                            return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear() + 543}`;
                        }

                        return defaultFormatter(date, format);
                    },
                    parseDate(dateStr, format) {
                        if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                            return defaultParser(dateStr, 'Y-m-d');
                        }

                        const match = dateStr.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                        if (match) {
                            return new Date(Number(match[3]) - 543, Number(match[2]) - 1, Number(match[1]));
                        }

                        return defaultParser(dateStr, format);
                    },
                });
            });
        },

        openScanModal() {
            this.form = { template_id: 'auto', batch_id: '', batch_name: '', labels: '' };
            this.files = [];
            this.fileName = '';
            this.loading = false;
            this.processingStatus = 'Start Extraction';
            this.modalOpen = true;
            const input = document.getElementById('imageInput');
            if (input) input.value = '';
        },

        closeModal() {
            if (!this.loading) this.modalOpen = false;
        },

        openBatchModal() {
            this.batchForm = { name: '', note: '' };
            this.batchError = '';
            this.batchLoading = false;
            this.batchModalOpen = true;
        },

        closeBatchModal() {
            if (!this.batchLoading) this.batchModalOpen = false;
        },

        openCollectionModal(payload = null) {
            const collection = payload || config.activeCollection;
            if (!collection) return;

            this.collectionForm = {
                id: String(collection.id || ''),
                name: collection.name || '',
                note: collection.note || '',
            };
            this.collectionError = '';
            this.collectionLoading = false;
            this.collectionModalOpen = true;
        },

        closeCollectionModal() {
            if (!this.collectionLoading) this.collectionModalOpen = false;
        },

        openSlipDetail(payload) {
            this.activeSlip = payload || null;
            this.detailOpen = true;
        },

        closeSlipDetail() {
            this.detailOpen = false;
            setTimeout(() => {
                if (!this.detailOpen) this.activeSlip = null;
            }, 180);
        },

        handleFileChange(event) {
            if (event.target.files.length > 0) {
                this.files = Array.from(event.target.files);
                this.fileName = this.files.length === 1 ? this.files[0].name : `${this.files.length} receipts selected`;
                return;
            }

            this.files = [];
            this.fileName = '';
        },

        appendBatchOption(batch) {
            document.querySelectorAll('[data-slip-batch-select]').forEach((select) => {
                let option = Array.from(select.options).find((candidate) => candidate.value === String(batch.id));
                if (!option) {
                    option = document.createElement('option');
                    option.value = String(batch.id);
                    select.appendChild(option);
                }
                option.textContent = batch.name;
            });
        },

        syncSelectedSelections() {
            this.selectedIds = Array.from(document.querySelectorAll('[data-slip-checkbox]:checked')).map((checkbox) => checkbox.value);
        },

        toggleAllSelections(event) {
            document.querySelectorAll('[data-slip-checkbox]').forEach((checkbox) => {
                checkbox.checked = event.target.checked;
            });
            this.syncSelectedSelections();
        },

        allOnPageSelected() {
            const checkboxes = Array.from(document.querySelectorAll('[data-slip-checkbox]'));
            return checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
        },

        selectedSummary() {
            const count = this.selectedIds.length;
            if (count === 0) return 'No slips selected';
            return `${count} slip${count === 1 ? '' : 's'} selected`;
        },

        requiresBulkLabel() {
            return this.bulkAction === 'add_label' || this.bulkAction === 'remove_label';
        },

        submitBulkForm(event) {
            this.syncSelectedSelections();

            if (this.selectedIds.length === 0) {
                event.preventDefault();
                Toast.fire({ icon: 'warning', title: 'Select slips first', text: 'Choose at least one slip before applying a bulk action.' });
                return;
            }

            if (this.requiresBulkLabel() && !this.bulkLabel.trim()) {
                event.preventDefault();
                Toast.fire({ icon: 'warning', title: 'Label required', text: 'Enter a label for this bulk action.' });
            }
        },

        async submitBatchForm() {
            if (!this.batchForm.name.trim()) {
                this.batchError = 'Collection name is required.';
                return;
            }

            this.batchLoading = true;
            this.batchError = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(config.createBatchRoute, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.batchForm),
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Unable to create collection.');

                this.appendBatchOption(data.collection || data.batch);
                this.form.batch_id = String((data.collection || data.batch).id);
                this.form.batch_name = '';
                this.batchModalOpen = false;
                Toast.fire({ icon: 'success', title: 'Collection ready' });
            } catch (error) {
                this.batchError = error.message || 'Unable to create collection.';
            } finally {
                this.batchLoading = false;
            }
        },

        async submitCollectionForm() {
            if (!this.collectionForm.id) {
                this.collectionError = 'Collection not found.';
                return;
            }

            if (!this.collectionForm.name.trim()) {
                this.collectionError = 'Collection name is required.';
                return;
            }

            this.collectionLoading = true;
            this.collectionError = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const route = String(config.updateBatchRouteTemplate || '').replace('__BATCH__', this.collectionForm.id);
                const response = await fetch(route, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.collectionForm.name,
                        note: this.collectionForm.note,
                    }),
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Unable to update collection.');

                this.appendBatchOption(data.collection);
                if (this.form.batch_id === this.collectionForm.id) {
                    this.form.batch_name = '';
                }
                this.collectionModalOpen = false;
                Toast.fire({ icon: 'success', title: 'Collection updated' });
                setTimeout(() => window.location.reload(), 600);
            } catch (error) {
                this.collectionError = error.message || 'Unable to update collection.';
            } finally {
                this.collectionLoading = false;
            }
        },

        async submitForm() {
            if (this.files.length === 0) {
                Toast.fire({ icon: 'error', title: 'Error', text: 'Please select at least one image file' });
                return;
            }

            this.loading = true;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            let successCount = 0;
            let errorCount = 0;

            for (let index = 0; index < this.files.length; index += 1) {
                this.processingStatus = `Processing ${index + 1} of ${this.files.length}...`;

                try {
                    const formData = new FormData();
                    formData.append('image', this.files[index]);
                    formData.append('template_id', this.form.template_id);
                    if (this.form.batch_id) formData.append('batch_id', this.form.batch_id);
                    if (this.form.batch_name.trim()) formData.append('batch_name', this.form.batch_name.trim());
                    if (this.form.labels.trim()) formData.append('labels', this.form.labels.trim());

                    const response = await fetch(config.processRoute, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
                        body: formData,
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || `File ${index + 1} failed`);
                    successCount += 1;
                } catch (error) {
                    console.error('Upload error:', error);
                    errorCount += 1;
                }
            }

            if (errorCount > 0) {
                Toast.fire({ icon: 'warning', title: 'Completed with Errors', text: `Processed: ${successCount}. Failed: ${errorCount}.` }).then(() => window.location.reload());
                return;
            }

            Toast.fire({ icon: 'success', title: `${successCount} slip(s) processed` });
            setTimeout(() => window.location.reload(), 900);
        },

        async deleteSlip(id) {
            if(!confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสลิปนี้?')) return;
            
            // 1. Optimistic UI: เก็บข้อมูลเดิมไว้ก่อน แล้วลบออกจากหน้าจอทันที
            const originalSlips = [...this.slips];
            this.slips = this.slips.filter(slip => slip.id !== id);
            const originalTotal = (this.pagination && this.pagination.total) ? this.pagination.total : this.slips.length + 1;
            if(this.pagination) {
                this.pagination.total = Math.max(0, (this.pagination.total || 1) - 1);
            }

            try {
                // 2. ส่งคำสั่งไปลบที่หลังบ้านเงียบๆ
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const route = `/workspace/slips/delete/${id}`;
                const res = await fetch(route, {
                    method: 'DELETE',
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken 
                    }
                });

                if (!res.ok) {
                    const errorData = await res.json().catch(() => ({}));
                    throw new Error(errorData.message || `Server error: ${res.status}`);
                }

                const data = await res.json();
                
                if(data.status !== 'success') {
                    throw new Error(data.message || 'Delete failed');
                }
                
                // ถ้ายอดรวมในหน้านี้น้อยเกินไป ให้ดึงข้อมูลใหม่เพื่อเอาสลิปหน้าถัดไปมาเติม
                if(this.slips.length < 5 && this.pagination && this.pagination.total > 0) {
                    this.fetchSlips();
                }

            } catch (error) {
                // 3. Rollback: ถ้าหลังบ้านมีปัญหา ให้ดึงข้อมูลสลิปกลับมาแสดงเหมือนเดิม
                console.error('Delete error:', error);
                alert('เกิดข้อผิดพลาด: ' + error.message);
                this.slips = originalSlips;
                if(this.pagination) {
                    this.pagination.total = originalTotal;
                }
            }
        },
    };
};
