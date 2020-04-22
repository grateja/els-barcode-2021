<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Client info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.ownerName" :error-messages="errors.get('ownerName')" outline label="Owner name" ref="ownerName"></v-text-field>
                    <v-text-field v-model="formData.shopName" :error-messages="errors.get('shopName')" outline label="Shop name"></v-text-field>
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
        'value', 'client'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                ownerName: null,
                shopName: null,
                address: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`client/${this.mode}Client`, {
                clientId: this.client ? this.client.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    client: res.data.client,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['client/getErrors'];
        },
        saving() {
            return this.$store.getters['client/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.client) {
                this.mode = 'update';
                this.formData.ownerName = this.client.owner_name;
                this.formData.shopName = this.client.shop_name;
                this.formData.address = this.client.address;
            } else {
                this.mode = 'insert';
                this.formData.ownerName = null;
                this.formData.shopName = null;
                this.formData.address = null;
            }
            setTimeout(() => {
                this.$refs.ownerName.$el.querySelector('input').select();
            }, 500);
        },
        client(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
