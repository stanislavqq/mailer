<template>
    <div>
        <aside class="left-menu">
            <mailer-menu></mailer-menu>
        </aside>
        <div class="mailer-content">
            <router-view></router-view>
        </div>
        <!--<mailer-templates></mailer-templates>-->
    </div>
</template>

<script>
    import MailerMenu from './layout/Menu';

    export default {
        name: "AppMailer",
        data() {
            return {}
        },
        components: {
            MailerMenu
        },
        methods: {
        }
    }
</script>

<style lang="scss">
    .content-head {
        overflow: hidden;
        margin: 30px auto 25px;
        border-bottom: 1px solid #e2e2e2;
        padding: 10px;
        max-width: 1200px;
    }

    .content-body {
        max-width: 1200px;
        margin: 0 auto;
    }

    img {
        max-width: 100%;
    }

    .title-wrap, .content-actions {
        width: 50%;
        float: left;
    }

    .content-actions {
        text-align: right;
        padding: 9px 0px;
    }

    .title {
        padding: 0;
        margin: 0;
        font-size: 36px;
        font-weight: 700;
        color: #5f5f5f;
    }

    aside.left-menu {
        max-width: 75px;
        width: 100%;
        float: left;
    }

    aside.left-menu + .mailer-content {
        margin-left: 75px;
    }

    .mailer-content {
        background-color: #fafafa;
        padding: 15px;
    }

    ul {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    #app_mailer {
        margin: -5px -14px -10px;
        border-top: 1px solid #333;
    }
</style>