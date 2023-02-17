<template>
    <div class="h2h__search position-relative" v-click-outside="closePredictions">
        <i class="fas fa-search icon"></i>
        <input type="text" @click="onTextFieldClick" v-model="search" @keydown="predict" placeholder="search athlete to compare" />
        <transition name="show">
            <div class="h2h__search-predictions" v-show="showPredictions && search != ''">
                <div v-if="predictions.length == 0">
                    <div class="p-2 text-center" v-if="loadingPredictions">
                        <img style="width: 20px;" src="/images/loading-white.svg" alt="Loading...">
                    </div>
                    <div class="px-3 py-2" v-else>{{__('No athletes found')}}.</div>
                </div>
                <a
                    href="#"
                    v-for="prediction in predictions"
                    :key="prediction.id"
                    @click.prevent="pickAthlete(prediction)"
                >
                    <img
                        v-if="prediction.country"
                        :src="`/images/flags/flags-mini/${prediction.country.code.toLowerCase()}.png`"
                        class="latest-results__micro-flag mr-2">
                    <div>
                        {{ prediction.firstName }} {{prediction.lastName}}
                    </div>
                </a>

                <div v-if="predictions.length > 0 && loadingPredictions" style="position:absolute;left:50%;top:50%;transform: translateX(-50%) translateY(-50%);">
                    <img src="/images/loading-white.svg" alt="Loading...">
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
import _ from 'lodash'
import ClickOutside from 'vue-click-outside'

export default {
    directives: {
        ClickOutside
    },
    props: ['value'],
    data() {
        return {
            search: '',
            predictions: [],
            loading: false,
            showPredictions: false,
            loadingPredictions: true,
        }
    },
    methods: {
        onTextFieldClick() {
            this.search = ''
            this.showPredictions = true
            if (this.predictions.length === 0) {
                this.predict()
            }
        },
        predict: _.debounce(async function() {
            if (this.search.length == 0) {
                this.showPredictions = false
                return
            }

            this.showPredictions = true
            this.loadingPredictions = true

            const { data } = await axios.post('/v1/athlete/predict', {
                q: this.search
            })

            this.loadingPredictions = false

            this.predictions = data
        }, 400),
        closePredictions() {
            this.showPredictions = false
            this.setSearchTextByAthlete(this.value)
        },
        async pickAthlete({ id }) {
            this.showPredictions = false
            this.loading = true
            const { data } = await axios.get(`/v1/athlete/${id}`)
            this.$emit('input', data)
            this.search = `${data.firstName} ${data.lastName}`
            this.loading = false
        },

        setSearchTextByAthlete(value) {
            if (value) {
                this.search = `${value.firstName} ${value.lastName}`
            }
        }
    },
    watch: {
        value(value) {
            this.setSearchTextByAthlete(value)
        }
    },
    mounted() {
        this.setSearchTextByAthlete(this.value)
    }
}
</script>
<style lang="scss" scoped>
.show-enter-active, .show-leave-active {
    transition: transform .2s, opacity .1s;
}
.show-enter, .show-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
    transform: scale(1, 0);
}

.h2h {

    &__search {
        input {
            border: 0;
            background: transparent;
            padding-left: 28px;
            color: #15174a;

            &:focus {
                outline:none;
            }
        }
        .icon {
            position:absolute;
            left:5px;
            top:50%;
            transform: translateY(-50%);
            margin-top: 1px;
            color: #15174a;
            pointer-events: none;
        }

        &-predictions {
            transform-origin: 50% 0;
            position: absolute;
            z-index: 100;
            top: 100%;
            left: 0;
            background: transparentize(#15174a, 0.05);
            min-width: 250px;
            border-radius: 4px;
            border-top-left-radius: 0;

            color: #fff;

            a {
                display: flex;
                align-items: center;
                padding: 5px 15px;
                color: #fff;
                transition: .1s background-color ease;

                &:hover, &:focus, &:active {
                    text-decoration: none;
                    background-color: lighten(#15174a, 12%);
                }
            }
        }
    }
}
</style>
