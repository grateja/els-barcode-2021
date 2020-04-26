<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Finished good info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.partNumber" :error-messages="errors.get('partNumber')" outline label="Part number" ref="partNumber"></v-text-field>
                    <v-text-field v-model="formData.description" :error-messages="errors.get('description')" outline label="Description"></v-text-field>
                    <v-text-field v-model="formData.specs" :error-messages="errors.get('specs')" outline label="Specs"></v-text-field>
                    <v-text-field v-model="formData.supplier" :error-messages="errors.get('supplier')" outline label="Supplier"></v-text-field>
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
        'value', 'sparePartProfile'
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
            this.$store.dispatch(`sparePartProfile/${this.mode}SparePartProfile`, {
                id: this.sparePartProfile ? this.sparePartProfile.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    sparePartProfile: res.data.sparePart,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['sparePartProfile/getErrors'];
        },
        saving() {
            return this.$store.getters['sparePartProfile/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.sparePartProfile) {
                this.mode = 'update';
                this.formData.partNumber = this.sparePartProfile.id;
                this.formData.description = this.sparePartProfile.description;
                this.formData.specs = this.sparePartProfile.specs;
                this.formData.supplier = this.sparePartProfile.supplier;
                this.formData.serialNumber = this.sparePartProfile.serial_number;
            } else {
                this.mode = 'insert';
                this.formData.partNumber = null;
                this.formData.description = null;
                this.formData.specs = null;
                this.formData.supplier = null;
                this.formData.serialNumber = null;
            }
            setTimeout(() => {
                this.$refs.partNumber.$el.querySelector('input').select();
            }, 500);
        },
        sparePartProfile(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
