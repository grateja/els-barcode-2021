<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Finished good info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.model" :error-messages="errors.get('model')" outline label="Model" ref="model"></v-text-field>
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
        'value', 'finishedGoodProfile'
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
            this.$store.dispatch(`finishedGoodProfile/${this.mode}FinishedGoodProfile`, {
                id: this.finishedGoodProfile ? this.finishedGoodProfile.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    finishedGoodProfile: res.data.finishedGood,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['finishedGoodProfile/getErrors'];
        },
        saving() {
            return this.$store.getters['finishedGoodProfile/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.finishedGoodProfile) {
                this.mode = 'update';
                this.formData.model = this.finishedGoodProfile.id;
                this.formData.description = this.finishedGoodProfile.description;
                this.formData.specs = this.finishedGoodProfile.specs;
                this.formData.supplier = this.finishedGoodProfile.supplier;
                this.formData.serialNumber = this.finishedGoodProfile.serial_number;
            } else {
                this.mode = 'insert';
                this.formData.model = null;
                this.formData.description = null;
                this.formData.specs = null;
                this.formData.supplier = null;
                this.formData.serialNumber = null;
            }
            setTimeout(() => {
                this.$refs.model.$el.querySelector('input').select();
            }, 500);
        },
        finishedGoodProfile(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
