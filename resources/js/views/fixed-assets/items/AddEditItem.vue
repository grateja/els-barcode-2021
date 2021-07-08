<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Issue item</v-card-title>
                <v-card-text>
                    <vuetify-autocomplete url="/api/autocomplete/accounts" label="Search employee id, name ..." ref="search" @select="selectProfile" v-model="employee" />
                    <v-text-field v-model="formData.employeeId" :error-messages="errors.get('employeeId')" outline label="Employee ID" ref="employeeId"></v-text-field>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" outline label="Account name" ref="name"></v-text-field>
                    <v-text-field v-model="formData.department" :error-messages="errors.get('department')" outline label="Department"></v-text-field>
                    <v-text-field v-model="formData.serialNumber" :error-messages="errors.get('serialNumber')" outline label="Serial number" ref="serialNumber"></v-text-field>
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
        'value', 'fixedAsset'
    ],
    data() {
        return {
            mode: 'insert',
            employee: null,
            formData: {
                employeeId: null,
                name: null,
                department: null,
                serialNumber: null,
                description: null,
                specs: null,
                dateIssued: moment().format('YYYY-MM-DD')
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`fixedAsset/${this.mode}FixedAsset`, {
                fixedAssetId: this.fixedAsset ? this.fixedAsset.serial_number : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    fixedAsset: res.data.fixedAsset,
                    mode: this.mode
                });
            });
        },
        selectProfile(data) {
            this.formData.employeeId = data.id;
            this.formData.name = data.name;
            this.formData.department = data.department;
            setTimeout(() => {
                this.$refs.serialNumber.$el.querySelector('input').select();
            }, 500);
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
                this.formData.employeeId = this.fixedAsset.account_id;
                this.formData.name = this.fixedAsset.account_name;
                this.formData.department = this.fixedAsset.department;
                this.formData.serialNumber = this.fixedAsset.serial_number;
                this.formData.description = this.fixedAsset.description;
                this.formData.specs = this.fixedAsset.specs;
                this.formData.dateIssued = moment(this.fixedAsset.date_issued).format('YYYY-MM-DD');
            } else {
                this.formData.name = null;
                this.formData.department = null;
                this.formData.serialNumber = null;
                this.formData.description = null;
                this.formData.specs = null;
                this.formData.dateIssued = moment().format('YYYY-MM-DD');
                this.formData.employeeId = null;
            }

            this.employee = null;

            setTimeout(() => {
                this.$refs.search.$el.querySelector('input').select();
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
