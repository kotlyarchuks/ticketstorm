<template>
    <div>
        <div class="col col-xs-6">
            <div class="form-group m-xs-b-4">
                <label class="form-label">
                    Qty
                </label>
                <input v-model="quantity" class="form-control">
            </div>
        </div>
        <vue-stripe-checkout
                ref="checkoutRef"
                :name="name"
                :description="description"
                :currency="currency"
                :amount="totalPrice"
                :allow-remember-me="false"
                @done="done"
                @opened="opened"
                @closed="closed"
                @canceled="canceled"
        ></vue-stripe-checkout>
        <button class="btn btn-primary" @click="checkout">Checkout</button>
    </div>
</template>

<script>
    export default {
        props: [
            'price',
            'concertTitle',
            'concertId',
        ],
        data() {
            return {
                name: 'Ticketstorm',
                currency: 'usd',
                quantity: 1,
            }
        },
        computed: {
            description(){
                if (this.quantity > 1) {
                    return `${this.quantity} tickets to ${this.concertTitle}`
                }
                return `One ticket to ${this.concertTitle}`
            },
            totalPrice(){
                return this.quantity * this.price
            },
            priceInDollars() {
                return (this.price / 100).toFixed(2)
            },
            totalPriceInDollars() {
                return (this.totalPrice / 100).toFixed(2)
            },
        },
        methods: {
            async checkout () {
                // token - is the token object
                // args - is an object containing the billing and shipping address if enabled
                const {token, args} = await this.$refs.checkoutRef.open();

                console.log(token);

                axios.post(`/concerts/${this.concertId}/orders`, {
                    email: token.email,
                    tickets: this.quantity,
                    token: token.id,
                }).then(response => {
                    console.log('Charge was successful')
                }).catch(response => {
                    console.log(response)
                })
            },
            done ({token, args}) {
            },
            opened () {
                // do stuff
            },
            closed () {
                // do stuff
            },
            canceled () {
                // do stuff
            }
        }
    }
</script>