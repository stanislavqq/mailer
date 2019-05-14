<template>
    <div class="page-contacts-add">
        <div class="content-head">
            <button class="btn btn-primary" @click="$router.push({name: 'Contacts'});"><i class="fa fa-arrow-left"></i>
            </button>
            <div class="title-wrap">
                <h2 class="title">Редактировать список {{ $route.params.id }}</h2>
            </div>
            <div class="content-actions">
                <button class="btn btn-success" @click="formContactEditSave()"><i class="fa fa-check"></i> Сохранить
                </button>
            </div>
        </div>
        <div class="content-body">
            <el-form v-model="formEdit">
                <el-form-item label="Название">
                    <el-input v-model="formEdit.name"></el-input>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input type="textarea"
                              :rows="2"
                              placeholder="Описание"
                              v-model="formEdit.description"></el-input>
                </el-form-item>

                <el-form-item label="Контакты">
                    <contact-list :form-data="formEdit"></contact-list>
                </el-form-item>
            </el-form>
        </div>

    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import ContactList from '../plagins/ContactList';

    export default {
        name: "ContactsEdit",
        components: {ContactList},
        //props: {formEdit},
        data() {
            return {}
        },
        computed: {
            ...mapGetters({
                formEdit: 'GET_CONTACT_LIST_ITEM'
            }),
        },
        created() {

        },
        methods: {
            handleClientDelete(index, row) {
                console.log(index, row);
            },
            formContactEditSave() {
                let self = this;

                this.$store.dispatch('UPDATE_CONTACT_LIST', this.formEdit)
                    .then(response => {
                        let res = this.$store.dispatch('SET_CONTACT_LIST_ITEM', this.$route.params.id);
                        if (res) {
                            this.$message({
                                message: 'Сохранено',
                                type: 'success'
                            });
                            self.$router.push({name: 'Contacts'});
                        } else {
                            this.$message.error('Ошибка');
                        }
                    });
            },
        },
        mounted() {
        }
    }
</script>

<style scoped type="SCSS">

</style>
