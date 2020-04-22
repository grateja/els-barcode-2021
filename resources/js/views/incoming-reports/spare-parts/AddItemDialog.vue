<template>
    <v-dialog :value="value" persistent max-width="840">
        <v-card>
            <v-card-title>
                <span class="grey--text">Add item</span>
                <v-spacer></v-spacer>
                <v-btn @click="close" icon small>
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-text-field label="Search serial number" outline @keyup="load" v-model="keyword" ref="keyword"></v-text-field>
            </v-card-text>
            <v-card flat color="transparent" min-height="600" style="overflow-y:auto" max-height="700px">

                <v-data-table v-model="selectedItems" item-key="serial_number" select-all :items="items" :headers="headers" hide-actions :loading="loading">
                    <template v-slot:items="props">
                        <tr :active="props.selected" @click="props.selected = !props.selected">
                            <td>
                                <v-checkbox :input-value="props.selected"></v-checkbox>
                            </td>
                            <td>{{props.index + 1}}</td>
                            <td>{{ props.item.serial_number }}</td>
                            <td>{{ props.item.part_number }}</td>
                            <td>{{ props.item.description }}</td>
                            <td>{{ props.item.specs }}</td>
                            <td>{{ props.item.supplier }}</td>
                            <td>
                                <v-btn small icon>
                                    <v-icon>delete</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table>

            </v-card>
            <v-card-actions>
                <v-btn round color="success" @click="createNew">Create new</v-btn>
                <v-btn round color="primary" @click="selectItems" v-if="selectedItems.length >= 1" :loading="addingItems">Select ({{selectedItems.length}})</v-btn>
            </v-card-actions>
        </v-card>
        <add-edit-dialog v-model="openAddEdit" @save="createItem" />
    </v-dialog>
</template>

<script>
import AddEditDialog from '../../spare-parts/AddEditDialog.vue';

export default {
    components: {
        AddEditDialog
    },
    props: [
        'value', 'reportId'
    ],
    data() {
        return {
            loading: false,
            addingItems: false,
            openAddEdit: false,
            cancelSource: null,
            keyword: null,
            selectedItems: [],
            items: [],
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Serial number',
                    sortable: false
                },
                {
                    text: 'Model',
                    sortable: false
                },
                {
                    text: 'Description',
                    sortable: false
                },
                {
                    text: 'Specs',
                    sortable: false
                },
                {
                    text: 'Supplier',
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
        close() {
            this.$emit('input', false);
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            this.loading = true;
            axios.get(`/api/spare-parts/items`, {
                params: {
                    keyword: this.keyword
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                console.log(res.data)
                this.items = res.data.result.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        createNew() {
            this.openAddEdit = true;
        },
        selectItems() {
            if(this.selectedItems.length < 1) {
                alert('No item selected');
                return false;
            }

            this._addItems(this.selectedItems.map(i => i.serial_number));
        },
        createItem(data) {
            this._addItems([data.sparePart.serial_number]);
        },
        _addItems(serialNumbers) {
            this.addingItems = true;
            this.$store.dispatch('incomingSparePartReport/addItems', {
                reportId: this.reportId,
                formData: {
                    serialNumbers: serialNumbers
                }
            }).then((res, rej) => {
                this.$emit('selectItems', {
                    items: res.data.items
                });
                this.$store.commit('setFlash', {
                    message: res.data.items.length + ' Item(s) added',
                    color: 'success'
                });
            }).finally(() => {
                this.addingItems = false;
            });
        }
    },
    watch: {
        value(val) {
            if(val) {
                setTimeout(() => {
                    this.$refs.keyword.$el.querySelector('input').select();
                }, 500);
            }
            this.items = [];
        }
    }
}
</script>
