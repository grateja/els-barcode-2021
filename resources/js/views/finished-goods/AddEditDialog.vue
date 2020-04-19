<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Finished good info</v-card-title>
                <v-card-text>
                    <vuetify-autocomplete url="/api/autocomplete/finished-goods" label="Search model, description ..." ref="serach" @select="selectProfile" />
                    <v-text-field v-model="formData.model" :error-messages="errors.get('model')" outline label="Model" hint="Search model here"></v-text-field>
                    <v-text-field v-model="formData.description" :error-messages="errors.get('description')" outline label="Description"></v-text-field>
                    <v-text-field v-model="formData.specs" :error-messages="errors.get('specs')" outline label="Specs"></v-text-field>
                    <v-text-field v-model="formData.supplier" :error-messages="errors.get('supplier')" outline label="Supplier"></v-text-field>
                    <v-text-field v-model="formData.serialNumber" :error-messages="errors.get('serialNumber')" outline label="Serial Number" ref="serialNumber"></v-text-field>
                    <v-text-field v-model="formData.warehouse" :error-messages="errors.get('warehouse')" outline label="Warehouse"></v-text-field>
                    <v-text-field v-model="formData.currentLocation" :error-messages="errors.get('currentLocation')" outline label="Current location"></v-text-field>
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
        'value', 'finishedGood'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                model: null,
                description: null,
                specs: null,
                supplier: null,
                serialNumber: null,
                warehouse: null,
                currentLocation: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`finishedGood/${this.mode}FinishedGood`, {
                serialNumber: this.finishedGood ? this.finishedGood.serial_number : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    finishedGood: res.data.finishedGood,
                    mode: this.mode
                });
            });
        },
        selectProfile(data) {
            this.formData.model = data.model;
            this.formData.description = data.description;
            this.formData.specs = data.specs;
            this.formData.supplier = data.supplier;
            setTimeout(() => {
                this.$refs.serialNumber.$el.querySelector('input').select();
            }, 500);
        }
    },
    computed: {
        errors() {
            return this.$store.getters['finishedGood/getErrors'];
        },
        saving() {
            return this.$store.getters['finishedGood/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.finishedGood) {
                this.mode = 'update';
                this.formData.model = this.finishedGood.model;
                this.formData.description = this.finishedGood.description;
                this.formData.specs = this.finishedGood.specs;
                this.formData.supplier = this.finishedGood.supplier;
                this.formData.serialNumber = this.finishedGood.serial_number;
                this.formData.warehouse = this.finishedGood.warehouse;
                this.formData.currentLocation = this.finishedGood.current_location;
            } else {
                this.mode = 'insert';
                this.formData.model = null;
                this.formData.description = null;
                this.formData.specs = null;
                this.formData.supplier = null;
                this.formData.serialNumber = null;
                this.formData.warehouse = null;
                this.formData.currentLocation = null;
            }
            setTimeout(() => {
                this.$refs.serach.$el.querySelector('input').select();
            }, 500);
        },
        finishedGood(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
