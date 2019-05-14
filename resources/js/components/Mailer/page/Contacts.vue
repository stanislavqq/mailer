<template>
    <div class="page-contacts">
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Контакты</h2>
            </div>
            <div class="content-actions">
                <router-link :to="{name: 'ContactsAdd'}" class="btn btn-success"><i class="fa fa-plus"></i> Создать
                    список
                </router-link>
            </div>
        </div>
        <div class="content-body">
            <el-table
                    :data="contacts_lists"
                    style="width: 100%">
                <el-table-column
                        label="Наименование"
                        width="200">
                    <template slot-scope="scope">
                        {{ scope.row.name }}
                    </template>
                </el-table-column>
                <el-table-column
                        label="Описание"
                        width="200">
                    <template slot-scope="scope">
                        {{ scope.row.description }}
                    </template>
                </el-table-column>
                <el-table-column
                        label="Кол-во контактов"
                        width="200">
                    <template slot-scope="scope">
                        {{ scope.row.contactCount }}
                    </template>
                </el-table-column>
                <el-table-column
                        label="Действия"
                        width="200">
                    <template slot-scope="scope">
                        <el-button type="primary" size="mini" circle icon="el-icon-edit"
                                   @click="handleContactEditClick(scope.row)">
                            <!--<router-link :to="">Редактировать</router-link>-->
                        </el-button>
                        <el-button type="danger" size="mini" circle icon="el-icon-delete"
                                   @click="handleListRemove(scope.$index, scope.row)"></el-button>

                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "Contacts",
        data() {
            return {
                //tableData: this.$store.state.tableData
            }
        },
        computed: {
            ...mapGetters({
                contacts_lists: "GET_CONTACTS_LISTS"
            })
        },
        methods: {
            handleContactEditClick(row) {
                console.log('handleContactEditClick', row);
                this.$store.dispatch('SET_CONTACT_LIST_ITEM', row.id);
                this.$router.push({ name: 'ContactsEdit', params: {id: row.id}});
            },
            handleListRemove(index, row) {
                console.log(index, row);
                let self = this;

                this.$confirm('Действительно хотите удалить список?', 'Warning', {
                    confirmButtonText: 'Да',
                    cancelButtonText: 'Отмена',
                    type: 'warning'
                }).then(() => {
                    self.$store.dispatch('DELETE_CONTACT_LIST', {
                        id: row.id
                    }).then(() => {
                        self.contacts_lists.splice(index, 1);
                        self.$message({
                            message: 'Список удален',
                            type: 'success'
                        });
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Удаление отменено'
                    });
                });
            }
        },
        created() {
            this.$store.dispatch("SET_CONTACT_LISTS");
        }
    }
</script>

<style scoped>

</style>
