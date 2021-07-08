<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Edit serial</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.serialNumber" :error-messages="errors.get('serialNumber')" outline label="Serial Number" ref="serialNumber"></v-text-field>
                    <v-text-field v-model="formData.description" :error-messages="errors.get('description')" outline label="Description"></v-text-field>
                    <v-text-field v-model="formData.specs" :error-messages="errors.get('specs')" outline label="Specs"></v-text-field>
                    <v-text-field v-model="formData.dateIssued" :error-messages="errors.get('dateIssued')" outline label="Issued" type="date"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'fixedAsset', 'accountId'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                serialNumber: null,
                description: null,
                specs: null,
                dateIssued: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`fixedAsset/${this.mode}Serial`, {
                accountId: this.accountId,
                serialNumber: this.fixedAsset ? this.fixedAsset.serial_number : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    fixedAsset: res.data.fixedAsset,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['fixedAsset/getErrors'];
        },
        saving() {
            return this.$store.getters['fixedAsset/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.fixedAsset) {
                this.mode = 'update';
                this.formData.serialNumber = this.fixedAsset.serial_number;
                this.formData.description = this.fixedAsset.description;
                this.formData.specs = this.fixedAsset.specs;
                this.formData.dateIssued = moment(this.fixedAsset.date_issued).format('YYYY-MM-DD');
            } else {
                this.mode = 'insert';
                this.formData.serialNumber = null;
                this.formData.description = null;
                this.formData.specs = null;
                this.formData.dateIssued = null;
            }
            setTimeout(() => {
                this.$refs.serialNumber.$el.querySelector('input').select();
            }, 500);
        },
        fixedAsset(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
