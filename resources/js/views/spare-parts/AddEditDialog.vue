<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Finished good info</v-card-title>
                <v-card-text>
                    <vuetify-autocomplete url="/api/autocomplete/spare-parts" label="Search part number, description ..." ref="serach" @select="selectProfile" />
                    <v-text-field v-model="formData.partNumber" :error-messages="errors.get('partNumber')" outline label="Part number"></v-text-field>
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
        'value', 'sparePart'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                partNumber: null,
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
            this.$store.dispatch(`sparePart/${this.mode}SparePart`, {
                serialNumber: this.sparePart ? this.sparePart.serial_number : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    sparePart: res.data.sparePart,
                    mode: this.mode
                });
            });
        },
        selectProfile(data) {
            this.formData.partNumber = data.partNumber;
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
            return this.$store.getters['sparePart/getErrors'];
        },
        saving() {
            return this.$store.getters['sparePart/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.sparePart) {
                this.mode = 'update';
                this.formData.partNumber = this.sparePart.part_number;
                this.formData.description = this.sparePart.description;
                this.formData.specs = this.sparePart.specs;
                this.formData.supplier = this.sparePart.supplier;
                this.formData.serialNumber = this.sparePart.serial_number;
                this.formData.warehouse = this.sparePart.warehouse;
                this.formData.currentLocation = this.sparePart.current_location;
            } else {
                this.mode = 'insert';
                this.formData.partNumber = null;
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
        sparePart(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
