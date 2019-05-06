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
                            @click="addContact()"
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

            <div class="scrolling">
            <el-table
                    id="filter-table"
                    v-loading="loading"
                    :data="clientsTableDataComputed"
                    style="width: 100%"
                    ref="multipleTable"
                    width="100%"
                    height="600px"
                    header-row-class-name="filter-head"
                    @row-click="rowClickHandle">
                <el-table-column
                        type="index"
                        width="55">
                </el-table-column>

                <el-table-column
                        prop="client_name"
                        width="250">
                    <template slot="header" slot-scope="scope">
                        <el-input
                                v-model="clientsFilters.client_name"
                                @change="eventFilterChange()"
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
                                size="mini"
                                placeholder="Город"
                                clearable/>
                    </template>
                </el-table-column>
            </el-table>
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
                formAddDialogVisible: false,
                formAddClientsDialog: {
                    addButtonDisabled: false
                },
                tableData: [],
                loading: false,
            }
        },
        computed: {
            // ...mapGetters({
            //     formEdit: 'GET_CONTACT_LIST_ITEM'
            // }),
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

                result = result.filter(item => {
                    return item.client_name !== null ? item.client_name.toLowerCase().includes(self.clientsFilters.client_name.toLowerCase()) : false;
                });

                result = result.filter(item => {
                    return item.name !== null ? item.name.toLowerCase().includes(self.clientsFilters.client_director.toLowerCase()) : false;
                });

                result = result.filter(item => {
                    return item.email !== null ? item.email.toLowerCase().includes(self.clientsFilters.client_email.toLowerCase()) : false;
                });

                result = result.filter(item => {
                    return item.phone !== null ? item.phone.toLowerCase().includes(self.clientsFilters.client_phone.toLowerCase()) : false;
                });

                result = result.filter(item => {
                    return item.city !== null ? item.city.toLowerCase().includes(self.clientsFilters.client_city.toLowerCase()) : false;
                });

                return result;
            }
        },
        methods: {
            handleClientDelete(index, row) {
                let list = this.formData.list;
                list.splice(index, 1);
            },
            addRowsFilters() {
                let self = this;
                this.clientsTableDataComputed.forEach(row => {
                    self.tableData.push(row);
                    console.log(self.formData);
                    self.formData.list.push(row);
                    self.clientsTableData.remove(row.id);
                });

            },
            eventFilterChange() {
                //usedFilter

                let addButtonFilterFlag = false;

                if (this.clientsFilters.client_director.length
                    || this.clientsFilters.client_name.length
                    || this.clientsFilters.client_email.length
                    || this.clientsFilters.client_phone.length
                ) {
                    addButtonFilterFlag = true
                }

                this.usedFilter = addButtonFilterFlag;
            },
            rowClickHandle(row, event, column) {

                console.log(this.formData);
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
            addContact() {
                let self = this;
                this.formAddDialogVisible = true;
                this.loading = true;

                let actionSetClients = this.$store.dispatch('SET_CLIENTS');
                actionSetClients.then(response => {
                    setTimeout(function () {
                        self.loading = false;
                    }, 500);
                });
            },
        },
        created() {

        },
        mounted() {

            console.log('clientsTableData', this.clientsTableData);

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
</style>