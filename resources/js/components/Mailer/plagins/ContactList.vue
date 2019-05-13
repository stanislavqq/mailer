<template>
    <div id="contact-list">
        <el-table
            :data="formEdit.list"
            height="600"
            style="width: 100%">

            <el-table-column
                prop="client_name"
                label="Название"
                width="180">
            </el-table-column>

            <el-table-column
                prop="name"
                label="ФИО"
                width="180">
            </el-table-column>

            <el-table-column
                prop="email"
                label="Email">
            </el-table-column>

            <el-table-column
                prop="phone"
                label="Телефон">
            </el-table-column>

            <el-table-column
                label="Действия">

                <template slot-scope="scope">
                    <el-button
                        size="mini"
                        type="danger"
                        icon="el-icon-delete"
                        circle
                        @click="handleClientDelete(scope.$index, scope.row)"></el-button>
                </template>

            </el-table-column>

            <el-table-column
                align="right">
                <template slot="header" slot-scope="scope">
                    <el-button
                        size="mini"
                        type="danger"
                        circle
                        @click="clearListSelectedContact()"
                        icon="el-icon-delete-solid">
                    </el-button>

                    <el-button
                        @click="addContact()"
                        size="mini"
                        type="success"
                        circle
                        icon="el-icon-plus"
                    >
                    </el-button>
                </template>
            </el-table-column>

        </el-table>
        <el-dialog
            title="Контакты клиентов"
            :visible.sync="formAddDialogVisible"
            width="70%"
        >
            <el-button
                type="primary"
                size="medium"
                @click="addRowsFilters()"
                :disabled="btnAddAllDisable"
            >{{ btnAddAllText }}
            </el-button>

            На странице: {{ clientsTableData.length }}

            <div class="scrolling">
                <el-table
                    id="filter-table"
                    v-loading="loading"
                    :data="clientsTableData"
                    style="width: 100%"
                    ref="multipleTable"
                    width="100%"
                    height="600px"
                    header-row-class-name="filter-head"
                    @row-click="rowClickHandle">

                    <el-table-column
                        prop="id"
                        width="70">
                        <template slot="header">ID</template>
                    </el-table-column>

                    <el-table-column
                        prop="client_name"
                        width="250">
                        <template slot="header" slot-scope="scope">
                            <el-input
                                v-model="clientsFilters.client_name"
                                @change="eventFilterChange()"
                                @clear="eventFilterClear()"
                                size="mini"
                                placeholder="Компания"
                                clearable/>
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="name"
                        width="250">
                        <template slot="header" slot-scope="scope">
                            <el-input
                                v-model="clientsFilters.client_director"
                                @change="eventFilterChange()"
                                @clear="eventFilterClear()"
                                size="mini"
                                placeholder="ФИО"
                                clearable/>
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="email"
                        width="180">
                        <template slot="header" slot-scope="scope">
                            <el-input
                                v-model="clientsFilters.client_email"
                                @change="eventFilterChange()"
                                @clear="eventFilterClear()"
                                size="mini"
                                placeholder="Email"
                                clearable/>
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="phone"
                        width="180">
                        <template slot="header" slot-scope="scope">
                            <el-input
                                v-model="clientsFilters.client_phone"
                                @change="eventFilterChange()"
                                @clear="eventFilterClear()"
                                size="mini"
                                placeholder="Телефон"
                                clearable/>
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="city"
                        width="180">
                        <template slot="header" slot-scope="scope">
                            <el-input
                                v-model="clientsFilters.client_city"
                                @change="eventFilterChange()"
                                @clear="eventFilterClear()"
                                size="mini"
                                placeholder="Город"
                                clearable/>
                        </template>
                    </el-table-column>
                </el-table>

                <el-pagination
                    class="table-clients-pagination"
                    background
                    @current-change="handleCurrentChange"
                    :current-page="pager.current"
                    :page-size="pager.perPage"
                    layout="prev, pager, next"
                    :total="clientsTotal">
                </el-pagination>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';

    export default {
        name: "ContactList",
        props: [
            'title',
            'action',
            'formData'
        ],
        data() {
            return {
                btnAddAllDisable: false,
                clientsFilters: {
                    client_name: '',
                    client_director: '',
                    client_email: '',
                    client_phone: '',
                    client_city: '',
                },
                pager: {
                    perPage: 50,
                    current: 1,
                },
                formAddDialogVisible: false,
                formAddClientsDialog: {
                    addButtonDisabled: false
                },
                tableData: [],
                loading: false,
            }
        },
        computed: {
            ...mapGetters({
                clientsTotal: "GET_CLIENTS_TOTAL"
            }),

            ...mapGetters({
                clientsTableData: "GET_CLIENTS"
            }),

            formEdit() {
                if (this.formData) {
                    return this.formData;
                }

                return {
                    list: []
                };
            },

            btnAddAllText() {

                for (let key in this.clientsFilters) {
                    if (this.clientsFilters[key].length > 0) {
                        return 'Добавить все по фильтру';
                    }
                }

                return 'Добавить все';
            },

            clientsTableDataComputed() {
                let self = this;
                let result = this.clientsTableData;

                if (this.action === 'create') {
                    return [];
                }

                result = this.filterColumn(result, 'client_name', 'client_name');
                result = this.filterColumn(result, 'name', 'client_director');
                result = this.filterColumn(result, 'email', 'client_email');
                result = this.filterColumn(result, 'phone', 'client_phone');
                result = this.filterColumn(result, 'city', 'client_city');

                return result;
            }
        },
        methods: {

            clearListSelectedContact() {
                this.formEdit.list = [];
            },

            /**
             *
             * @param data array
             * @param colName string
             * @param filterColName string
             * @returns data modifed
             */
            filterColumn(data, colName, filterColName) {
                let self = this;

                if (self.clientsFilters[filterColName].length) {
                    return data.filter(item => {
                        return item[colName] !== null ? item[colName].toLowerCase().includes(self.clientsFilters[filterColName].toLowerCase()) : false;
                    });
                }

                return data;
            },

            /**
             * handleCurrentChange(val)
             * Handle event change current page after click a pagination element
             *
             * @param val
             */
            handleCurrentChange(val) {
                this.pager.current = val;

                this.setClients();
                console.log(`current page: ${val}`, this.clientsTotal);
            },

            /**
             * handleClientDelete(index, row)
             * Remove client row data from list
             *
             * @param index
             * @param row
             */
            handleClientDelete(index, row) {
                let list = this.formData.list;
                list.splice(index, 1);
            },

            /**
             *
             */
            addRowsFilters() {
                let self = this;
                this.clientsTableDataComputed.forEach(row => {
                    self.tableData.push(row);
                    console.log(self.formData);
                    self.formData.list.push(row);
                    self.clientsTableData.remove(row.id);
                });

            },

            eventFilterClear() {
                this.filterSearch();
            },
            /**
             *
             */
            eventFilterChange() {
                this.filterSearch();
            },

            filterSearch() {
                //usedFilter

                let addButtonFilterFlag = false;

                if (this.clientsFilters.client_director.length
                    || this.clientsFilters.client_name.length
                    || this.clientsFilters.client_email.length
                    || this.clientsFilters.client_phone.length
                    || this.clientsFilters.client_city.length
                ) {
                    addButtonFilterFlag = true;
                    this.setClients();
                }

                this.usedFilter = addButtonFilterFlag;
            },

            /**
             *
             * @param row
             * @param event
             * @param column
             */
            rowClickHandle(row, event, column) {

                if (row) {
                    this.tableData.push(row);
                    this.formData.list.push(row);
                    this.clientsTableData.remove(row.id);
                    this.$message({
                        type: 'info',
                        message: 'Добавлено'
                    });
                }
            },

            /**
             *
             */
            addContact() {
                let self = this;
                this.formAddDialogVisible = true;
                this.loading = true;

                let actionSetClients = this.setClients();
                actionSetClients.then(response => {
                    setTimeout(function () {
                        self.loading = false;
                    }, 500);
                });
            },

            /**
             *
             * @returns {Promise<any>}
             */
            setClients() {
                let offset = this.pager.perPage * this.pager.current;
                let limit = this.pager.perPage;
                let filter = this.clientsFilters;

                return this.$store.dispatch('SET_CLIENTS', {
                    offset: offset,
                    limit: limit,
                    filter: filter
                });
            }

        },

        mounted() {
            Array.prototype.remove = function (id) {
                let arrayLength = this.legnth;

                for (let index in this) {
                    let row = this[index];
                    if (row.id === id) {
                        this.splice(index, 1);
                        break;
                    }
                }

                return this;
            };
        }
    }
</script>

<style scoped type="SCSS">
    .table-clients-pagination {
        margin: 20px 0;
    }
</style>
