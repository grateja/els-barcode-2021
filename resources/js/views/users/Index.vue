<template>
    <v-container>
        <h3 class="title grey--text">Users</h3>

        <v-divider class="my-3"></v-divider>

        <v-btn round class="primary" @click="addUser">
            <v-icon left>add</v-icon>
            add user
        </v-btn>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.email }}</td>
                <td>{{ props.item.contact_number }}</td>
                <td>
                    <v-btn small icon @click="edit(props.item)">
                        <v-icon small>edit</v-icon>
                    </v-btn>
                    <v-btn small icon @click="deleteUser(props.item)" :loading="props.item.isDeleting">
                        <v-icon small>delete</v-icon>
                    </v-btn>
                    <v-btn small @click="changePassword(props.item)" flat outline>
                        <v-icon small left>sms</v-icon> change password
                    </v-btn>
                </td>
            </template>
        </v-data-table>
        <user-dialog :user="activeUser" v-model="openUserDialog" @save="updateUsers" />
        <password-dialog :user="activeUser" v-model="openPasswordDialog" />
    </v-container>
</template>

<script>
import UserDialog from './UserDialog.vue';
import PasswordDialog from './PasswordDialog.vue';

export default {
    components: {
        UserDialog,
        PasswordDialog
    },
    data() {
        return {
            items: [],
            loading: false,
            activeUser: null,
            openUserDialog: false,
            openPasswordDialog: false,
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Email',
                    sortable: false
                },
                {
                    text: 'Contact number',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/users').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        edit(user) {
            this.activeUser = user;
            this.openUserDialog = true;
        },
        addUser() {
            this.activeUser = null;
            this.openUserDialog = true;
        },
        changePassword(user) {
            this.activeUser = user;
            this.openPasswordDialog = true;
        },
        updateUsers(data) {
            if(data.mode == 'insert') {
                this.items.push(data.user);
                this.activeUser = data.user;
            } else {
                this.activeUser.name = data.user.name;
                this.activeUser.contact_number = data.user.contact_number;
                this.activeUser.email = data.user.email;
            }
        },
        deleteUser(user) {
            if(confirm('Delete this user?')) {
                Vue.set(user, 'isDeleting', true);
                this.$store.dispatch('user/deleteUser', user.id).then((res, rej) => {
                    this.items = this.items.filter(d => d.id != user.id);
                }).finally(() => {
                    Vue.set(user, 'isDeleting', false);
                });
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
