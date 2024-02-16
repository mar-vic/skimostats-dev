<template>
    <div class="homepage__upcoming-races">
        <div>
            <div class="position-relative pt-5 pt-xl-0">
                <div class="d-flex align-items-center justify-content-center homepage__upcoming-races-title">
                    <div class="latest-results__left-text font-family-heading text-blue">
                        <div>{{__('Upcoming')}}</div>
                        {{__('Races')}}
                    </div>
                </div>
                <div class="pb-5 pt-0 pt-xl-5">
                    <div class="container py-5">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <span class="text-uppercase font-weight-bold mr-2">Filter:</span><br>
                                    <a href="#"
                                        @click.prevent="selectedCategory = null"
                                        class="badge badge--custom mr-1 my-1 text-uppercase"
                                        :class="{'badge-active': selectedCategory === null}">{{__('all')}}</a>
                                    <a href="#"
                                        v-for="c in filteredCategories"
                                        :key="`up-${c.name}`"
                                        @click.prevent="selectedCategory = c"
                                        class="badge badge--custom mr-1 my-1 text-uppercase"
                                        :class="{'badge-active': selectedCategory && selectedCategory.name === c.name}">{{ c.name }}</a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table--races table--races-striped text-uppercase">
                                        <thead>
                                            <tr>
                                                <th style="width:70px;0">{{__('Date')}}</th>
                                                <th>{{__('Race')}}</th>
                                                <th style="width: 100px;">{{__('Category')}}</th>
                                                <th class="text-center">
                                                    <div style="cursor:help;" data-bs-toggle="tooltip" data-bs-placement="right" title="You can rent a place to stay at race event location by clicking on map symbols below. (we get comissions for click-throughs)">
                                                        <svg width="64" height="22" viewBox="0 0 144 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M141.483 27.1072C130.078 26.854 123.932 26.854 123.042 27.1072C121.709 27.4869 117.658 33.7634 116.672 33.7443C115.685 33.7253 111.946 27.4942 110.518 27.1072C109.567 26.8492 103.525 26.8492 92.3929 27.1072C91.0026 27.1072 89.8755 25.9817 89.8755 24.5933V2.51383C89.8755 1.12548 91.0026 0 92.3929 0H141.483C142.873 0 144 1.12548 144 2.51383V24.5943C143.999 25.9823 142.873 27.1072 141.483 27.1072ZM114.049 8.08952C115.286 8.08952 116.292 8.9221 116.292 9.94523C116.292 10.8085 115.621 11.8819 114.299 13.1348L108.771 18.3636V20.6703H119.646V17.7039H113.554L116.205 15.1941C118.4 13.183 119.513 11.3771 119.513 9.83161C119.512 8.54075 118.929 7.31876 117.926 6.5048C116.85 5.5814 115.468 5.09042 114.049 5.12722C111.589 5.10371 109.359 6.5698 108.408 8.83613L111.285 10.2157C111.846 8.84467 112.828 8.08952 114.052 8.08952H114.049ZM101.384 8.08952C102.621 8.08952 103.627 8.9221 103.627 9.94523C103.627 10.8075 102.956 11.8809 101.634 13.1348L96.1061 18.3636V20.6703H106.981V17.7039H100.889L103.541 15.1941C105.735 13.183 106.848 11.3771 106.848 9.83161C106.848 8.54075 106.265 7.31876 105.261 6.5048C104.185 5.58144 102.803 5.09047 101.385 5.12722C98.9248 5.10446 96.6959 6.57047 95.7456 8.83613L98.6225 10.2157C99.1834 8.84467 100.165 8.08952 101.387 8.08952H101.384ZM64.7258 44H64.7218C64.3486 43.9817 63.9987 43.8128 63.7519 43.5318C63.5006 43.2137 63.3955 41.9506 63.3693 41.5685C63.3266 40.9518 63.2884 39.8659 63.5166 39.5185C63.8635 39.1525 64.3594 38.9668 64.861 39.0151C64.9329 39.0151 64.9842 39.0151 65.0048 39.0186C67.2924 39.0186 68.5865 37.9151 69.7781 34.9585L71.0475 31.7839C70.3708 30.0849 62.8098 11.0932 62.6614 10.5898C62.629 10.4778 62.6526 10.357 62.7248 10.2656C63.032 9.85375 64.4407 9.66849 65.3427 9.66849C66.1632 9.66849 67.1918 9.80039 67.4307 10.4282C67.7766 11.3373 72.5041 22.7646 73.6257 25.475C74.7127 22.7424 79.2004 11.4798 79.778 10.3995C80.133 9.73444 81.1063 9.59398 81.861 9.59398C82.8379 9.59398 84.0732 9.8457 84.3708 10.2484C84.4474 10.3461 84.4716 10.475 84.4357 10.5938C84.2718 11.1148 74.579 35.6643 74.1662 36.7079V36.7114C73.0903 39.3242 71.9339 41.0686 70.5277 42.2023C69.0194 43.4196 67.1758 43.9904 64.7308 43.999L64.7258 44ZM19.5267 14.5737C19.1686 14.5364 19.4595 10.5545 20.0317 10.5485C20.6038 10.5424 22.9606 10.5485 22.9606 10.5485C22.9606 10.5485 22.9576 6.5469 22.9606 6.36878C22.9637 6.19067 27.5561 5.27996 27.5561 6.0201C27.5561 6.76023 27.5561 10.5485 27.5561 10.5485C27.5561 10.5485 32.7979 10.5188 33.414 10.5485C34.0301 10.5782 33.6857 14.5737 33.414 14.5737H27.5561V25.6933C27.5264 26.5033 27.8258 27.291 28.3866 27.8782C28.9474 28.4653 29.7221 28.8023 30.5355 28.8129C31.1674 28.8336 31.7999 28.8168 32.4298 28.7626C33.01 28.6775 33.667 28.3505 33.67 28.9991C33.6731 29.6476 33.7534 31.9943 33.67 32.579C33.5867 33.1636 30.2325 33.1903 30.2325 33.1903C28.3074 33.2422 26.439 32.5374 25.0311 31.228C23.6814 29.9229 22.9322 28.1206 22.9606 26.2468V14.5737C22.9606 14.5737 19.8847 14.6109 19.5267 14.5737ZM59.1198 33.5957C58.9028 33.7007 58.6509 33.7533 58.3975 33.7431C57.3345 33.6603 56.2887 33.4278 55.2911 33.0529V30.3525L54.4942 31.048C52.5759 32.7223 50.1056 33.6267 47.5601 33.5866C46.0823 33.6008 44.6296 33.3128 43.2803 32.7406C41.9547 32.152 40.7542 31.2982 39.7575 30.2333C37.6698 27.9652 36.5505 24.9664 36.6416 21.8807V21.8525C36.5507 18.7679 37.6695 15.7701 39.7588 13.4995C40.7527 12.4375 41.9532 11.5838 43.2862 10.992C44.6286 10.4218 46.0819 10.133 47.548 10.1466C50.1056 10.1066 52.5759 11.0109 54.4942 12.6852L55.2911 13.3807V10.7163C56.1365 10.5888 56.9909 10.5166 57.8474 10.5005C58.2539 10.4888 58.6554 10.5309 59.0459 10.6256C59.3343 10.7092 59.4001 10.7905 59.4012 10.8939C59.4091 11.7077 59.43 17.4942 59.4355 21.7097C59.4438 28.1668 59.4248 32.4349 59.3712 33.1896C59.3631 33.3693 59.2674 33.5192 59.1198 33.5957ZM43.2979 26.9363C44.5199 28.2648 46.252 29.0066 48.0565 28.9744C49.8475 29.0187 51.5677 28.2741 52.7612 26.9379C54.0631 25.5764 54.765 23.7497 54.7098 21.8666C54.7649 19.9852 54.0642 18.16 52.7642 16.7989C51.5706 15.4605 49.8492 14.7145 48.0565 14.7588C46.2526 14.7273 44.5214 15.4693 43.2999 16.7974C42.0327 18.1762 41.3523 19.9945 41.4033 21.8666C41.3523 23.7387 42.0327 25.5571 43.2999 26.9358L43.2979 26.9363ZM17.4174 15.7038C17.6283 16.6445 13.2549 16.6943 13.1889 16.4576C13.0717 16.1125 12.906 15.7857 12.6966 15.4872C11.8418 14.4244 10.5713 13.945 8.75905 13.945C6.59446 13.945 4.9836 14.8998 4.9836 16.3571C4.9836 17.3119 5.73869 18.0657 7.19853 18.6184L12.2828 20.3773C15.9576 21.6838 17.8201 23.8447 17.8201 26.8598C17.8201 31.3323 14.1454 33.7444 9.11143 33.7444C3.92647 33.7444 0.604073 31.282 0 27.4126C0 27.4126 4.14494 26.1935 4.32919 26.7593C4.43404 27.1914 4.61672 27.601 4.86832 27.9679C5.78903 29.099 7.29115 29.7242 9.2121 29.7242C11.6284 29.7242 13.491 28.6689 13.491 27.0106C13.491 25.905 12.5848 25.0507 10.7223 24.3974L5.68835 22.7391C2.21493 21.5331 0.604073 19.6737 0.604073 16.7089C0.604073 12.2364 4.12783 9.92481 9.11143 9.92481C13.1361 9.92481 16.02 11.5947 17.0147 14.2279C17.2002 14.7045 17.3352 15.1992 17.4174 15.7038Z" fill="#243047"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <upcoming-race-row
                                                v-for="event in filteredRaces"
                                                :event="event"
                                                :category="category"
                                                :key="`ev-${event.id}`"
                                            />
                                        </tbody>
                                    </table>
                                </div>

                                <div class="modal fade"
                                    id="stay22ModalContainer"
                                    tabindex="-1"
                                    aria-labelledby="stay22Modal"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <stay22-embed
                                                :location="stay22Data.location"
                                                :checkin="stay22Data.checkin"
                                                :checkout="stay22Data.checkin"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <a href="/races" class="d-inline-flex align-items-center">{{__('Show all races')}}<img src="/images/arrow-right-black.png" class="mt-1 ml-2"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import { mapGetters } from 'vuex'

import UpcomingRaceRow from './upcoming-races/UpcomingRaceRow.vue'
import Stay22Embed from "../../shared/components/Stay22Embed.vue"

const shownCategories = [
    {
        name: 'Senior',
        slug: 'men',
        age: 'adult',
    },
    {
        name: 'Junior',
        slug: 'junior-men',
        age: 'junior',
    },
    {
        name: 'Cadet',
        slug: 'cadet-men',
        age: 'cadet',
    },
    {
        name: 'Masters',
        slug: 'masters-men',
        age: 'master',
    },
]

export default {
    data() {
        return {
            selectedCategory: null,
            stay22Data: {
                location: "",
                checkin: "",
                checkout: "",
            },
        }
    },
    components: {
        UpcomingRaceRow,
        Stay22Embed,
    },
    computed: {
        ...mapGetters('upcoming', ['races', 'categories']),

        filteredRaces() {
            return this.races.filter(race => {
                return race.categories.find(cat => !this.selectedCategory || this.selectedCategory.age === cat.age)
            }).sort((a,b) => moment(a.startDate).diff(b.startDate)).slice(0, 10)
        },

        filteredCategories() {
            return shownCategories
        },

        category() {
            return this.selectedCategory
        }
    },
    watch: {
        categories(newVal) {
            if (newVal.length) {
                this.selectedCategory = null
            }
        }
    },
}
</script>
<style scoped>
.homepage__upcoming-races tbody tr:nth-child(odd) {
    background-color: #fce000;
}
</style>
