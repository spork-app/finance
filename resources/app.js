Spork.setupStore({
    Finance: require("./store").default,
})


Spork.routesFor('finance', [
    Spork.authenticatedRoute('/finance', require('./Finance/Finance').default, {
        children: [
            Spork.authenticatedRoute('dashboard', require('./Finance/Dashboard').default),
            Spork.authenticatedRoute('bills', require('./Finance/Billing').default),
            Spork.authenticatedRoute('groups', require('./Finance/Groups').default),
            Spork.authenticatedRoute('settings', require('./Finance/Settings').default),
        ]
    }),
]);