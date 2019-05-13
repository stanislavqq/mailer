<template>
    <div>
        <ul class="mailer-menu">
            <li v-for="rLink in routeLinks">
                <router-link :to="rLink.to" :title="rLink.title">
                    <div><i class="el" :class="rLink.icon"></i></div>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "Menu",
        data() {
            return {
                routeLinks: [
                    {
                        title: 'Шаблоны',
                        to: {name: 'mailerTemplates'},
                        icon: "el-icon-s-open",
                    },

                    {
                        title: 'Рассылки',
                        to: {name: 'Distribution'},
                        icon: "el-icon-s-promotion",
                    },

                    {
                        title: 'Контакты',
                        to: {name: 'Contacts'},
                        icon: "el-icon-s-custom",
                    },
                    {
                        title: 'Настройки',
                        to: {name: 'MailerSettings'},
                        icon: "el-icon-s-tools",
                    },
                ]
            }
        },
        methods: {
        }
    }
</script>

<style scoped lang="scss">
    .mailer-menu {
        padding: 0;
        margin: 0;
        list-style: none;
        background-color: #fafafa;
        li:not(:last-child) {
            border-bottom: 1px solid #5a5a5a;
        }
        a {
            background-color: #424242;
            padding: 20px 10px;
            display: block;
            color: #fff;
            font-size: 24px;
            text-align: center;
            -webkit-transition: all ease 300ms;
            -moz-transition: all ease 300ms;
            -ms-transition: all ease 300ms;
            -o-transition: all ease 300ms;
            transition: all ease 300ms;
            &:hover {
                background-color: #d26f68;
            }
        }
    }
</style>
