<template>
    <div>
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Шаблоны</h2>
            </div>
            <div class="content-actions">
                <router-link :to="{name: 'TemplateAdd'}" class="btn btn-success"><i class="fa fa-plus"></i> Создать
                </router-link>
            </div>
        </div>
        <div class="content-body">
            <div class="templates-list">

                <el-table :data="templatesItems">
                    <el-table-column
                            prop="name"
                            label="Наименование"
                            width="200">
                    </el-table-column>
                    <el-table-column
                            prop="description"
                            label="Описание"
                            width="300">
                    </el-table-column>
                    <el-table-column
                            prop="updated_at"
                            label="Обновлено"
                            width="200">
                    </el-table-column>
                    <el-table-column
                            fixed="right"
                            label="Действия"
                            width="200"

                    >
                        <template slot-scope="scope">
                            <el-button type="primary" circle="" size="small" icon="el-icon-edit" @click="edit(scope.row)"></el-button>
                            <el-button type="danger" circle="" size="small" icon="el-icon-delete" @click="removeTemplate(scope.row.id)"></el-button>
                        </template>
                    </el-table-column>d
                </el-table>

            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import moment from 'moment';

    export default {
        name: "Templates",
        data() {
            return {
                //templatesItems: []
            }
        },
        computed: {
            ...mapGetters({
                templatesItems: "GET_TEMPLATES_DATA",
                templateVariables: "GET_TEMPLATE_VARIABLES"
            }),

        },
        methods: {
            edit(row) {
                console.log(row);
                this.$router.push({name: 'TemplateEdit', params: {id: row.id}});
            },
            removeTemplate(id) {
                let self = this;

                this.$confirm('Вы действительно хотите удалить этот шаблон?', 'Подтвердите удаление', {
                    confirmButtonText: 'Да',
                    cancelButtonText: 'Отмена',
                    type: 'warning'
                }).then(() => {
                    axios.post('mailer/template/remove', {id: id})
                        .then(response => {
                            self.$store.dispatch("SET_TEMPLATES")
                                .then(() => {
                                    self.$message({
                                        message: 'Шаблон успешно удален',
                                        type: 'info'
                                    });
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
            console.log(this.templatesItems);

        },
        filters: {
            filterUpdatedDate(dateString) {
                return moment(dateString).format('DD MMM HH:mm')
            }
        }
    }
</script>

<style scoped lang="scss">
    .teaser-title {
        font-size: 36px;
    }

    .template-author {

    }

    .templates-list {
        overflow: hidden;
        .link-edit {
            display: block;
            width: 100%;
            height: 100%;
            padding: 60px 0 0;
            color: #fff;
        }
        .template-teaser-content {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            -webkit-transition: all ease 300ms;
            -moz-transition: all ease 300ms;
            -ms-transition: all ease 300ms;
            -o-transition: all ease 300ms;
            transition: all ease 300ms;
        }
        .template-item {
            position: relative;
            &:hover {
                .template-teaser-content {
                    opacity: 1;
                }
            }
        }
    }

    .margin-top-bottom {
        margin: 15px 0;
    }


</style>