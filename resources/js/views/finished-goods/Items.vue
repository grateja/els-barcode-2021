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
            <v-btn round color="success" small @click="addItem" v-if="actions && actions.addItem">
                <v-icon left>add</v-icon>
                add item
            </v-btn>

            <v-btn v-if="selectedItems && selectedItems.length && actions && actions.print" round small>
                <v-icon left>print</v-icon>
                Print ({{selectedItems.length}})
            </v-btn>

            <v-btn v-if="selectedItems && selectedItems.length && actions && actions.removeItems" round small @click="removeItems" :loading="removingItems" >
                <v-icon left>clear</v-icon>
                Remove ({{selectedItems.length}})
            </v-btn>

        </v-card-actions>
    </v-card>
</template>
<script>
export default {
    props: [
        'params',
        'urlSrouce',
        'actions'
    ],
    data() {
        return {
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
            axios.get(this.urlSrouce, {
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
        }
    },
    watch: {
        urlSrouce:  {
            handler(val, oldVal) {
                console.log('url source', val);
                if(val) this.load();
            },
            deep: true,
            immediate: true
        }
    }
}
</script>
