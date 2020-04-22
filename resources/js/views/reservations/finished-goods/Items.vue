<template>
    <v-card>
        <v-data-table v-model="selectedItems" item-key="serial_number" select-all :items="items" :headers="headers" hide-actions :loading="loading">
            <template v-slot:items="props">
                <tr :active="props.selected" @click="props.selected = !props.selected">
                    <td>
                        <v-checkbox :input-value="props.selected"></v-checkbox>
                    </td>
                    <td>{{props.index + 1}}</td>
                    <td>{{ props.item.serial_number }}</td>
                    <td>{{ props.item.model }}</td>
                    <td>{{ props.item.description }}</td>
                    <td>{{ props.item.specs }}</td>
                    <td>{{ props.item.supplier }}</td>
                </tr>
            </template>
        </v-data-table>
        <v-card-actions>
            <v-btn round color="success" small @click="addItem">
                <v-icon left>add</v-icon>
                add item
            </v-btn>

            <v-btn v-if="selectedItems && selectedItems.length" round small>
                <v-icon left>print</v-icon>
                Print ({{selectedItems.length}})
            </v-btn>

            <v-btn v-if="selectedItems && selectedItems.length" round small @click="removeItems" :loading="removingItems" >
                <v-icon left>clear</v-icon>
                Remove ({{selectedItems.length}})
            </v-btn>

        </v-card-actions>
        <add-item-dialog v-model="openAddItem" :reservationId="reservationId" @selectItems="updateItems" />
    </v-card>
</template>
<script>
import AddItemDialog from './AddItemDialog.vue';

export default {
    components: {
        AddItemDialog
    },
    props: [
        'reservationId'
    ],
    data() {
        return {
            openAddItem: false,
            removingItems: false,
            page: 1,
            reset: true,
            selectedItems: [],
            cancelSrouce: null,
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
                }
            ],
            loading: false
        }
    },
    methods: {
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            this.loading = true;
            axios.get(`/api/reservations/finished-goods/${this.reservationId}/view-items`, {
                params: this.params,
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                console.log(res.data);
                if(this.reset) {
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
                this.loading = false;
            }).finally(() => {
                this.loading = false;
            });
        },
        loadMore() {
            this.page += 1;
            this.reset = false;
            this.load();
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        addItem(){
            this.openAddItem = true;
        },
        updateItems(data) {
            if(data.items) {
                this.items = [...this.items, ...data.items];
            }
        },
        removeItems() {
            if(this.selectedItems.length < 1) {
                alert('No items selected');
                return;
            }
            if(confirm('Remvoe this item(s) from this report?')) {
                this.removingItems = true;
                this.$store.dispatch('reservation/removeFinishedGoods', {
                    reservationId: this.reservationId,
                    formData: {
                        serialNumbers: this.selectedItems.map(i => i.serial_number)
                    }
                }).then((res, rej) => {
                    this.items = this.items.filter(i => res.data.serialNumbers.indexOf(i.serial_number) < 0);
                }).finally(() => {
                    this.removingItems = false;
                });
            }
        }
    },
    watch: {
        reservationId: {
            handler(val) {
                if(val) {
                    this.load();
                } else {
                    this.items = [];
                }
            },
            deep: true,
            immediate: true
        }
    }
}
</script>
