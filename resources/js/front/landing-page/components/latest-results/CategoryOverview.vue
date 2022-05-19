<template>
    <div class="latest-results__race-category mb-5" v-if="showCategory">
        <h2 class="text-uppercase font-weight-bold mb-3">{{ category.name }}</h2>
        <a :href="`/athlete/${firstEntry.participants[0].slug}`" class="latest-results__first mb-3">
            <div class="latest-results__photo position-relative"
                :style="`background-image:url(${firstEntry.participants[0].avatar?firstEntry.participants[0].avatar:'/images/sample-photo.jpg'});`">
                <div class="latest-results__number latest-results__number--first">01</div>
            </div>
            <div class="latest-results__info">
                <div class="font-weight-bold">{{ joinParticipants(firstEntry.participants) }}</div>
                <div class="small mb-2">{{firstEntry.prependTime ? firstEntry.prependTime + ' ' : ''}}{{ firstEntry.timeFormatted }}</div>
                <div class="d-flex justify-content-between align-items-center">
                    <img
                        v-if="firstEntry.participants[0].country"
                        class="latest-results__mini-flag"
                        :src="`/images/flags/flags-mini/${firstEntry.participants[0].country.toLowerCase()}.png`"
                        :alt="firstEntry.participants[0].country"
                        />
                    <div class="latest-results__view-profile">View profile</div>
                </div>
            </div>
        </a>
        <a  :href="`/athlete/${entry.participants[0].slug}`" class="latest-results__result d-flex align-items-center pl-1 mb-2" v-for="entry in restEntries" :key="entry.id">
            <div class='latest-results__number mr-2'>{{ String(entry.rank).padStart(2, '0') }}</div>
            <div>
                <div class="text-uppercase font-family-heading font-weight-bold m-0">{{ joinParticipants(entry.participants) }}</div>
                <div class="d-flex align-items-center">
                    <img
                        v-if="entry.participants[0].country"
                        :src="`/images/flags/flags-mini/${entry.participants[0].country.toLowerCase()}.png`"
                        :alt="entry.participants[0].country"
                        class="latest-results__micro-flag mr-1" />
                    <div class="small">{{entry.prependTime ? entry.prependTime + ' ' : ''}}{{ entry.timeFormatted }}</div>
                </div>
            </div>
        </a>

        <div class="text-center" v-if="!isDetail">
            <a :href="`/event/${event.slug}/${category.slug}`">Show all results<img src="/images/arrow-right-black.png" class="ml-2" /></a>
        </div>
    </div>
</template>
<script>
export default {
    props: ['category', 'isDetail', 'event'],

    computed: {
        entries() {
            return this.category.entries;
        },
        hasEntries() {
            return this.entries && this.entries.length > 0
        },
        firstEntry() {
            return this.entries[0]
        },
        restEntries() {
            return this.entries.filter(a => !a.status).slice(1, 3)
        },
        showCategory() {
            return this.hasEntries
        },
    },
    methods: {
        joinParticipants(participants) {
            return participants.map(a => a.name).join(', ')
        }
    },
}
</script>
