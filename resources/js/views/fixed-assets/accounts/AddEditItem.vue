<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Account info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.employeeId" :error-messages="errors.get('employeeId')" outline label="Employee ID" ref="employeeId"></v-text-field>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" outline label="Name"></v-text-field>
                    <v-text-field v-model="formData.department" :error-messages="errors.get('department')" outline label="Department"></v-text-field>
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
        'value', 'account'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                employeeId: null,
                name: null,
                department: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`fixedAssetAccount/${this.mode}Account`, {
                accountId: this.account ? this.account.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    account: res.data.account,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['fixedAssetAccount/getErrors'];
        },
        saving() {
            return this.$store.getters['fixedAssetAccount/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.account) {
                this.mode = 'update';
                this.formData.employeeId = this.account.id;
                this.formData.name = this.account.name;
                this.formData.department = this.account.department;
            } else {
                this.mode = 'insert';
                this.formData.employeeId = null;
                this.formData.name = null;
                this.formData.department = null;
            }
            setTimeout(() => {
                this.$refs.employeeId.$el.querySelector('input').select();
            }, 500);
        },
        account(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
