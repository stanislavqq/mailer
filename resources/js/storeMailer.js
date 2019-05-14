import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const state = {
    tableData: [],
    templates: [],
    templateVariables: [],
    clients: [],
    currentUser: null,
    clientsTotal: 0,
    contact_lists: [],
    edit_contact_list: {
        id: 0,
        name: '',
        description: '',
        list: []
    },
    distributions: [],
    smtpProfiles: [],
    smtpUserProfile: {
        login: '',
        password: '',
        driver_id: ''
    }
};

const actions = {
        async SET_MAILER_DRIVERS({state, commit}) {
            axios.post('/mailer/drivers/get')
                .then(response => {
                    let data = response.data.drivers;
                    let userProfile = response.data.user_driver;

                    if (userProfile !== null) {
                        commit("UPDATE_STATE", {name: "smtpUserProfile", value: userProfile});
                    }

                    commit("UPDATE_STATE", {name: "smtpProfiles", value: data});
                })
                .catch(error => {
                })
        },
        async SAVE_MAILER_DRIVER_USER({state, commit}, params) {
            axios.post('/mailer/drivers/save', params)
                .then(response => {
                    if (response.data.status === 'ok') {
                        console.log('driver has been saved!');
                    }
                })
                .catch(error => {
                })
        },
        async SET_TEMPLATES({state, commit}, params) {
            axios.post('mailer/template/get_list')
                .then(response => {
                    if (response.status == 200) {
                        state.templates = response.data.templates;
                        state.templateVariables = response.data.variables;
                    }
                })
            //state.templates = tableData;
        },

        async SET_CLIENTS({state, commit}, params) {
            console.log('store test', params);
            axios.post('/mailer/contact/get_clients_list', params)
                .then(response => {
                    let clients = response.data.clients;
                    let clientsTotal = response.data.clients_total;

                    if (response.status === 200 && clients) {
                        state.clients = JSON.parse(clients);
                        state.clientsTotal = JSON.parse(clientsTotal);
                    }
                })
                .catch(response => {
                    console.error(response)
                });
        },

        async SET_CURRENT_USER({state, commit}) {
            axios.post('/mailer/get_user')
                .then(response => {
                    state.currentUser = response.data;
                });
        },

        async SET_CONTACT_LISTS({state, commit}, id) {
            axios.post('/mailer/contact/get_list')
                .then(response => {
                    console.log(response);
                    let list = response.data;
                    if (response.status === 200 && list) {
                        state.contact_lists = list;
                    }
                })
        },
        async SET_CONTACT_LIST_ITEM({state, commit}, contactListId) {
            console.log('SET_CONTACT_LIST_ITEM request begin');
            axios.post('/mailer/contact/get_item', {
                id: contactListId
            })
                .then(response => {
                    let list = response.data;
                    if (response.status === 200 && list) {
                        state.edit_contact_list = list;
                    }
                })
        },

        // async SET_DISTRIBUTION_ITEM({state, commit}, id) {
        //     axios.post('/mailer/distribution/get', {id: id})
        //         .then(response => {
        //             let list = response.data;
        //             if (response.status === 200 && list) {
        //                 state.distributions = list;
        //             }
        //         })
        // },

        async SAVE_CONTACT_LIST({state}, data) {
            return axios.post('/mailer/contact/save', data)
                .then(response => {
                    if (response.data.status == 'ok') {
                        return true;
                    }
                    return false;
                });
        },
        async UPDATE_CONTACT_LIST({state}, data) {
            axios.post('/mailer/contact/update', data)
                .then(response => {
                    if (response.data.status == 'ok') {
                        return true;
                    }
                    return false;
                });
        },

        async UPDATE_DISTRIBUTION({state}, data) {
            axios.post('/mailer/distribution/update', data)
                .then(response => {
                    if (response.data.status == 'ok') {
                        return true;
                    }
                    return false;
                });
        },

        async DELETE_CONTACT_LIST({state}, data) {
            axios.post('/mailer/contact/remove', data)
                .then(response => {
                    if (response.data.status == 'ok') {
                        return true;
                    }

                    return false;
                });
        }
        ,

        async SET_DISTRIBUTIONS({state}) {
            axios.post('/mailer/distribution/get')
                .then(response => {
                    state.distributions = JSON.parse(response.data);
                });
        }
        ,

        async UNSET_DISTRIBUTION({state}, id) {
            axios.post('/mailer/distribution/remove', {id: id})
                .then(response => {
                    store.dispatch("SET_DISTRIBUTIONS");
                });
        }
    }
;

const getters = {
    GET_TABLE_DATA(state) {
        return state.tableData;
    },
    GET_TEMPLATES_DATA(state) {
        return state.templates;
    },
    GET_TEMPLATE_VARIABLES(state) {
        return state.templateVariables;
    },
    GET_CONTACTS_LISTS(state) {
        return state.contact_lists;
    },
    GET_CLIENTS(state) {
        return state.clients;
    },
    GET_CLIENTS_TOTAL(state) {
        return state.clientsTotal;
    },
    GET_CONTACT_LIST_ITEM(state) {
        return state.edit_contact_list;
    },
    GET_DISTRIBUTIONS(state) {
        return state.distributions;
    },
    GET_DISTRIBUTIONS_BY_INDEX(state) {
        return state.distributions;
        //return distributions;
    },
    GET_CURRENT_USER(state) {
      return state.currentUser;
    },
};

const mutations = {
    UPDATE_STATE(state, {name, value}) {
        state[name] = value;
    }
};


const store = new Vuex.Store({
    state,
    actions,
    getters,
    mutations
});

export default store;
