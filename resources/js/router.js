// import Vue from 'vue'

import MailerTemplates from "./components/mailer/page/Templates"
import TemplateAdd from "./components/mailer/page/TemplateAdd"
import TemplateEdit from "./components/mailer/page/TemplateEdit"

import Contacts from './components/mailer/page/Contacts'
import ContactsAdd from './components/mailer/page/ContactsAdd'
import ContactsEdit from './components/mailer/page/ContactsEdit'

import Distribution from "./components/mailer/page/Distribution"
import DistributionEdit from "./components/mailer/page/DistributionEdit"
import DistributionAdd from "./components/mailer/page/DistributionAdd"
import DistributionSend from './components/mailer/page/DistributionSend'

import MailerSettings from './components/mailer/page/MailerSettings';

/*window.Vue.use(VueRouter)*/

const routes = [
    {
        name: 'mailerTemplates',
        path: '/#mailer',
        component: MailerTemplates,
    },
    {
        name: 'TemplateAdd',
        path: '/#mailer/templates/add',
        component: TemplateAdd
    },
    {
        name: 'TemplateEdit',
        path: '/#mailer/templates/edit/:id',
        component: TemplateEdit
    },
    {
        name: 'Distribution',
        path: '/#mailer/distribution',
        component: Distribution
    },
    {
        name: 'DistributionAdd',
        path: '/#mailer/distribution/add',
        component: DistributionAdd
    },
    {
        name: 'Contacts',
        path: '/#mailer/contacts',
        component: Contacts
    },
    {
        name: 'ContactsAdd',
        path: '/#mailer/contacts/add',
        component: ContactsAdd
    },
    {
        name: 'ContactsEdit',
        path: '/#mailer/contacts/edit',
        component: ContactsEdit
    },
    {
        name: 'DistributionSend',
        path: '/#mailer/distribution/send',
        component: DistributionSend
    },
    {
        name: 'DistributionEdit',
        path: '/#mailer/distribution/edit',
        component: DistributionEdit
    },
    {
        name: 'MailerSettings',
        path: '/#mailer/settings',
        component: MailerSettings
    },
]

export default  routes
