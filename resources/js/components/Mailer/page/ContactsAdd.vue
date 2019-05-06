<template>
    <div class="page-contacts-add">
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Новый список контактов</h2>
            </div>
            <div class="content-actions">
                <button class="btn btn-success" @click="formAddSave()"><i class="fa fa-check"></i> Сохранить
                </button>
            </div>
        </div>
        <div class="content-body">
            <el-form v-model="formAdd">
                <el-form-item label="Название">
                    <el-input v-model="formAdd.name"></el-input>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input type="textarea"
                              :rows="2"
                              placeholder="Описание"
                              v-model="formAdd.description"></el-input>
                </el-form-item>

                <el-form-item label="Контакты">
                    <contact-list :form-data="formListData"></contact-list>
                </el-form-item>
            </el-form>
        </div>
    </div>

</template>

<script>
    import {mapGetters} from "vuex";
    import ContactList from '../plagins/ContactList';

    export default {
        name: "ContactsAdd",
        components: {ContactList},
        data() {
            return {
                clientsFilters: {
                    client_name: '',
                    client_director: '',
                    client_email: '',
                    client_phone: '',
                },
                formAdd: {
                    name: '',
                    description: '',
                    contacts_list: []
                },
                contactsTableData: [],
                formLabelWidth: '200px',
                tableData: [],
                formAddDialogVisible: false,
                formAddClientsDialog: {
                    addButtonDisabled: false
                },
                loading: false,
                multipleSelection: []
            }
        },
        computed: {
            ...mapGetters({
                clientsTableData: "GET_CLIENTS"
            }),
            ...mapGetters({
                formListData: 'GET_CONTACT_LIST_ITEM'
            }),
        },
        methods: {
            rowClickHandle(row, event, column) {
                if (row) {
                    this.tableData.push(row);
                    this.formAdd.contacts_list.push(row);
                }
            },
            addContact() {
                let self = this;
                this.formAddDialogVisible = true;
                this.loading = true;

                this.$store.dispatch('SET_CLIENTS')
                    .finally(() => self.loading = false);
            },
            dialoghide() {
                this.formAddDialogVisible = false;
            },

            handleSelectionChange(val) {
                this.multipleSelection = val;
            },

            formClear() {
                this.formAdd = {
                    name: '',
                    description: '',
                    contacts_list: []
                };
            },
            formAddSave() {
                let self = this;
                this.$store.dispatch('SAVE_CONTACT_LIST', this.formAdd)
                    .then(response => {
                        self.$router.push({name: 'Contacts'});
                    }).catch(errorResponse => {
                        let dataError = JSON.parse(JSON.stringify(errorResponse));

                        for(let key in dataError.response.data.errors) {
                            let rowErrors = dataError.response.data.errors[key];

                            for(let i = 0; i < rowErrors.length;i++) {
                                let row = rowErrors[i];
                                self.$message({
                                    type: 'error',
                                    message: row
                                })
                            }
                        }
                });
            }
        },
        mounted() {

        },
        destroyed() {
            this.formClear();
        }
    }
</script>

<style scoped type="scss">
    .el-table__row {
        &:hover {
            cursor: pointer;
        }
    }
</style>