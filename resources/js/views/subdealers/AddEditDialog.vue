<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Subdealer info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.subdealerName" :error-messages="errors.get('subdealerName')" outline label="Name" ref="subdealerName"></v-text-field>
                    <v-text-field v-model="formData.companyName" :error-messages="errors.get('companyName')" outline label="Company name"></v-text-field>
                    <v-text-field v-model="formData.address" :error-messages="errors.get('address')" outline label="Address"></v-text-field>
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
        'value', 'subdealer'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                subdealerName: null,
                companyName: null,
                address: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`subdealer/${this.mode}Subdealer`, {
                subdealerId: this.subdealer ? this.subdealer.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    subdealer: res.data.subdealer,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['subdealer/getErrors'];
        },
        saving() {
            return this.$store.getters['subdealer/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.subdealer) {
                this.mode = 'update';
                this.formData.subdealerName = this.subdealer.subdealer_name;
                this.formData.companyName = this.subdealer.company_name;
                this.formData.address = this.subdealer.address;
            } else {
                this.mode = 'insert';
                this.formData.subdealerName = null;
                this.formData.companyName = null;
                this.formData.address = null;
            }
            setTimeout(() => {
                this.$refs.subdealerName.$el.querySelector('input').select();
            }, 500);
        },
        subdealer(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
