export default {
    getters: {
        manualFinance: (state, getters, rootState, rootGetters) => (rootGetters?.features?.finance || []).filter(account => account.name === 'Manual accounts')[0],

        allAccountsFromFeatures: (state, getters, rootState, rootGetters) => rootGetters.features?.finance?.reduce((accounts, ac) => ([
            ... new Set([
                ...accounts,
                ...ac.accounts,
            ])
        ]), []),
        transactions: state => state.transactions

    },
    state: {
        transactions: [],
        pagination: {},
        loading: false,
        errors: null,
    },
    actions: {
        async getTransactions({ state, getters }) {
            state.loading = true;
            try {
                const { data } = await axios.get(buildUrl('/api/transaction', {
                    filter: {
                        for_accounts: getters.allAccountsFromFeatures?.map(account => account.account_id).join('|'),
                    },
                    sort: '-date',
                    action: 'paginate:100'
                }));
                const { data: things, ...pagination} = data;

                state.pagination = pagination;
                state.transactions = things;

            } catch (e) {
                if (e?.response?.status === 422) {
                    state.errors = e.response.data
                }

                console.error(e)
            } finally {
                setTimeout(()=> state.loading = false, 500);
            }
        },
        
        async createAccount({ state }, account) {
            state.loading = true;
            try {
                await axios.post('/api/account', account);
            } catch (e) {
                if (e.response.status === 422) {
                    state.errors = e.response.data
                }
            } finally {
                setTimeout(()=> state.loading = false, 500);
            }
        }
    },
    
}