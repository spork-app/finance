<template>
    <div class="flex flex-wrap mt-4">
        <div class="w-full py-2 px-4 text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center justify-between">
            <span>Bills Dashboard</span>
            <span>
                <feature-required feature="budget" />
            </span>
        </div>
        <div class="w-full pb-4 px-4 text-base font-base text-gray-500 dark:text-gray-300">Greetings! It's 2021-01-01. You should expect $420.20 to be withdrawn today.</div>
        <!-- The billing information -->
        <div class="w-full flex flex-wrap py-4 m-4 bg-white dark:bg-gray-600 rounded-lg shadow">
            <div class="w-full flex mx-4 rounded-full overflow-none bg-gray-200 my-2 text-white font-bold items-center">
                <div :style="'width: '+((paidAmount/mtdAvailableAmount) * 100)+'%; border-radius: 50px 0 0 50px;'" class="bg-green-500 p-4 text-center"></div>
                <div :style="'width: '+((reservedAmount/mtdAvailableAmount) * 100)+'%;'" class="bg-yellow-500 p-4 text-center"></div>
                <div :style="'width: '+(((availableAmount - reservedAmount)/mtdAvailableAmount) * 100)+'%; border-radius: 0 50px 50px 0'" class="flex-grow bg-blue-600 p-4 text-center"></div>
            </div>
            <div class="w-full flex flex-col mx-4 gap-2 mt-2 pl-4">
                <div class="flex items-center gap-2"><span class="bg-green-500 p-2 w-4 h-4 rounded-full"></span>Paid (${{ paidAmount }})</div>
                <div class="flex items-center gap-2"><span class="bg-yellow-500 p-2 w-4 h-4 rounded-full"></span>Reserved for bills/savings (${{ reservedAmount }})</div>
                <div class="flex items-center gap-2"><span class="bg-blue-500 p-2 w-4 h-4 rounded-full"></span>Slush (${{ Math.round((availableAmount - (reservedAmount)) * 100)/ 100}})</div>
            </div>
        </div>
        
        <div class="w-full px-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-50">
                Month to date
            </h3>

            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div v-for="item in stats" :key="item.name" class="px-4 py-5 bg-white dark:bg-gray-600 shadow rounded-lg overflow-hidden sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300 truncate">
                    {{ item.name }}
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ item.stat }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="w-1/3">
            <div class="m-4 text-xl font-medium">Paid bills</div>
            <div class="bg-white dark:bg-gray-600 shadow overflow-hidden sm:rounded-md m-4 px-4 py-2 flex flex-col max-h-4xl divide-y divide-gray-200 items-center">
                <div class="flex w-full items-center gap-2" v-for="transaction in paidBills" :key="transaction">
                    <div class="w-8">
                        <check-icon class="text-green-500 fill-current"></check-icon>
                    </div>  

                    <div class="flex flex-wrap w-full items-center py-2">
                        <div class="w-full flex justify-between items-center">
                            <div class="font-medium">{{ transaction.name }}</div>
                            <div class="text-right text-gray-700 dark:text-gray-200 font-bold">${{ transaction.amount}}</div>
                        </div>

                        <div class="w-full flex justify-between items-center text-sm text-gray-600">
                            <div class="text-gray-500 dark:text-gray-300">{{ transaction.category }}</div>
                            <div class="text-right">{{ transaction.date }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-1/3">
            <div class="m-4 text-xl font-medium">Pending bills</div>
            <div class="bg-white dark:bg-gray-600 shadow overflow-hidden sm:rounded-md m-4 px-4 py-2 flex flex-col max-h-4xl divide-y divide-gray-200 items-center">
                <div class="flex w-full items-center gap-2" v-for="transaction in pendingBills" :key="transaction">
                    <div class="w-8">
                        <refresh-icon class="text-yellow-500 fill-current"></refresh-icon>
                    </div>  
                        
                    <div class="flex flex-wrap w-full items-center py-2">
                        <div class="w-full flex justify-between items-center">
                            <div class="font-medium">{{ transaction.name }}</div>
                            <div class="text-right text-gray-700 dark:text-gray-200 font-bold">${{ transaction.amount}}</div>
                        </div>

                        <div class="w-full flex justify-between items-center text-sm text-gray-600">
                            <div class="text-gray-500 dark:text-gray-300">{{ transaction.category }}</div>
                            <div class="text-right">{{ transaction.date }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="w-1/3">
            <div class="m-4 text-xl font-medium">Future Bills</div>
            <div class="bg-white dark:bg-gray-600 shadow overflow-hidden sm:rounded-md m-4 px-4 py-2 flex flex-col max-h-4xl divide-y divide-gray-200 items-center">
               <div class="flex w-full items-center gap-2" v-for="transaction in futureBills" :key="transaction">
                    <div class="w-8">
                        <clock-icon class="text-yellow-600 fill-current"></clock-icon>
                    </div>  
                        
                    <div class="flex flex-wrap w-full items-center py-2">
                        <div class="w-full flex justify-between items-center">
                            <div class="font-medium">{{ transaction.name }}</div>
                            <div class="text-right text-gray-700 dark:text-gray-200 font-bold">${{ transaction.amount}}</div>
                        </div>

                        <div class="w-full flex justify-between items-center text-sm text-gray-600">
                            <div class="text-gray-500 dark:text-gray-300">{{ transaction.category }}</div>
                            <div class="text-right">{{ transaction.date }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { 
    CheckCircleIcon,
    ChevronRightIcon, 
    MailIcon, 
    CheckIcon, 
    RefreshIcon, 
    ClockIcon,
    UsersIcon,
    MailOpenIcon,
    CursorClickIcon,
} from '@heroicons/vue/solid'
import CategoryIcon from "@components/CategoryIcon";

export default {
    name: "FinanceDashboard",
    components: {
        CheckCircleIcon,
        ChevronRightIcon,
        MailIcon,
        CategoryIcon,
        CheckIcon,
        RefreshIcon,
        ClockIcon
    },
    setup() {
        return {
            dayjs,
            transactions: [
                {
                    date: '2020-01-02',
                    name: 'Rent',
                    amount: 1300,
                    category: 'Rent',
                    pending: false
                },
                {
                    date: '2020-01-01',
                    name: 'Google One',
                    amount: 20.20,
                    category: 'Subscription',
                    pending: true
                },
                {
                    date: '2020-01-01',
                    name: 'AutoLoan *****1008',
                    amount: 100,
                    category: 'Debt',
                    pending: false
                }
            ],
            bills: [
                {
                    name: 'Rent',
                    category: 'Rent',
                    amount: 500,
                    due_date: '2020-01-01'
                },
                {
                    name: 'Google One',
                    category: 'Subscription',
                    amount: 100,
                    due_date: '2020-01-01'
                },
                {
                    name: 'Loan *****8301',
                    category: 'Debt',
                    amount: 400.0,
                    due_date: '2020-01-01'
                },
                {
                    name: 'Netflix',
                    category: 'Subscription',
                    amount: 15.99,
                    due_date: '2020-01-15'
                },
                {
                    name: 'Hulu',
                    category: 'Subscription',
                    amount: 14.99,
                    due_date: '2020-01-15'
                },
                {
                    name: 'Spotify',
                    category: 'Subscription',
                    amount: 12.99,
                    due_date: '2020-01-15'
                },
            ]
        }
    },
    methods: {
        transactionIsInBills(transaction) {
            return this.bills.map(bill => bill.name).includes(transaction.name)
        },
        billInTransactions(bill) {
            return this.transactions.filter(transaction => transaction.name === bill.name).length > 0
        },
    },
    computed: {
        // Paid bills
        paidBills() {
            return this.transactions.filter(transaction => this.transactionIsInBills(transaction) && !transaction.pending)
        },
        // Bills that are past due, but not paid or pending
        pendingBills() {
            return this.transactions.filter(transaction => this.transactionIsInBills(transaction) && transaction.pending);
        },
        futureBills() {
            return this.bills.filter(bill => !this.billInTransactions(bill));
        },
        paidAmount() {
            return this.paidBills.map(bill => bill.amount).reduce((a, b) => a + b, 0);
        },
        reservedAmount() {
            return this.pendingBills.map(bill => bill.amount).reduce((a, b) => a + b, 0) + this.futureBills.map(bill => bill.amount).reduce((a, b) => a + b, 0);
        },
        availableAmount() {
            return 1620;
        },
        mtdAvailableAmount() {
            return 2600;
        },
        stats() {
            return [
                { id: 1, name: 'Available Balance', stat: '$1,620', icon: UsersIcon },
                { id: 2, name: 'Bills that need to be paid', stat: '$' + this.reservedAmount, icon: MailOpenIcon },
                { id: 3, name: 'Available to spend', stat: '$' + (1620 - this.reservedAmount), icon: CursorClickIcon },
            ];
        }
    },
    mounted() {
        this.$store.dispatch('getTransactions', this.$store.getters.accounts)
    }
}
</script>
