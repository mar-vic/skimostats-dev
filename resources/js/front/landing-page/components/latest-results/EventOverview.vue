<template>
    <div class="container">
      <div class="latest-results__race-header text-center text-md-left">
        <h1 class="text-uppercase font-weight-bold mb-0">
          {{ event.name }}
          <span class="d-block float-none float-md-right">{{ eventDate }}</span>
        </h1>

        <div class="font-weight-bold text-uppercase mb-1">{{ raceType }}</div>
        <div class="small text-disabled">
          {{ event.resultCount }} {{__('results')}} &nbsp;&nbsp;<span v-if="event.elevation">|&nbsp;&nbsp; {{ event.elevation }}m &nbsp;&nbsp;</span>|&nbsp;&nbsp; {{ futureEvent?__('starts'):__('finished') }} {{ finishedAgo }} <span v-if="event.stageSlugs && isDetail">|&nbsp;&nbsp; stages: <span style="margin-right:5px;" v-for="(stageSlug, index) in event.stageSlugs"><a v-if="event.slug != stageSlug" :href="`/event/${stageSlug}`"><strong>{{index}}{{getOrdinal(index)}}</strong></a><span style="color:rgba(33, 37, 41, 0.5)" v-else><strong>{{index}}{{getOrdinal(index)}}</strong></span></span></span><span v-if="event.gcSlug && event.gcSlug != event.slug && isDetail">|&nbsp;&nbsp; <a :href="`/event/${event.gcSlug}`"><strong>General Classification</strong></a></span>
        </div>
      </div>
      <div class="position-relative">
        <a href="#" v-if="showNext" @click.prevent="carouselNext" class="carousel-right"><span class="fa fa-chevron-right"></span></a>
        <a href="#" v-if="showPrev" @click.prevent="carouselPrev" class="carousel-left"><span class="fa fa-chevron-left"></span></a>
        <div class="latest-results__race-results overflow-hidden">
          <!-- <carousel ref="owlcarousel" :items="4" :nav="false" :dots="false"> -->
          <div class="owl-carousel">
            <category-overview
              v-for="category in categories"
              :key="'cat' + category.id + 'ev' + event.id"
              :category="category"
              :event="event"
              :is-detail="isDetail">
            </category-overview>
          </div>
          <!-- </carousel> -->
        </div>
      </div>
    </div>
</template>

<script>
import moment from 'moment'
import CategoryOverview from './CategoryOverview.vue'

const owlCarouselOptions = {
  nav: false,
  dots: false,
  items: 4,
  center: false,
  responsive : {
    // breakpoint from 0 up
    0 : {
      items : 1,
      center: true,
    },
    // breakpoint from 480 up
    480 : {
      items : 2,
      center: true,
    },
    // breakpoint from 768 up
    768 : {
      items : 3,
    },
    1100: {
      items: 4,
    },
  }
}

export default {
  components: {
    CategoryOverview,
  },
  props: {
    event: {
      required: true,
    },
    isDetail: {
      default: false,
    },
  },
  data() {
    return {
      showPrev: false,
      showNext: false,
    }
  },
  watch: {
    event(nevVal) {
      this.refreshCarousel()
    },
  },
  computed: {
    categories() {
      return this.event.categories || []
    },

    eventDate() {
      return moment(this.event.startDate).format('DD/MM/YY')
    },

    raceType() {
      return this.event.raceType ? this.event.raceType.name : ''
    },

    futureEvent() {
      return moment(this.event.endDate || this.event.startDate).diff(moment(), 's') > 0
    },

    finishedAgo() {
      const diff = moment(this.event.endDate || this.event.startDate).diff(moment(), 's')
      return moment.duration(diff, 's').humanize(true)
    },
  },
  methods: {
    refreshCarousel() {
      $('.owl-carousel').owlCarousel('destroy')
      this.$nextTick(() => {
        this.initCarousel()
      })
    },

    initCarousel() {
      var $owl = $('.owl-carousel')
      $owl.unbind('refreshed.owl.carousel initialized.owl.carousel changed.owl.carousel')
      $owl.on('refreshed.owl.carousel initialized.owl.carousel changed.owl.carousel', (event) => {
        if (event.item.index === 0 && event.item.count !== 1) {
          this.showPrev = false
        }
        if (event.item.index > 0) {
          this.showPrev = true
        }
        if (event.item.index < event.item.count - event.page.size) {
          this.showNext = true
        }
        if (event.item.index >= event.item.count - event.page.size) {
          this.showNext = false
        }
      })
      $owl.owlCarousel(owlCarouselOptions)
    },

    carouselPrev() {
      $('.owl-carousel').trigger('prev.owl.carousel')
    },
    carouselNext() {
      $('.owl-carousel').trigger('next.owl.carousel')
    },

    getOrdinal(n) {
      let ord = 'th';

      if (n % 10 == 1 && n % 100 != 11)
      {
        ord = 'st';
      }
      else if (n % 10 == 2 && n % 100 != 12)
      {
        ord = 'nd';
      }
      else if (n % 10 == 3 && n % 100 != 13)
      {
        ord = 'rd';
      }

      return ord;
    }

  },
  mounted() {
    this.initCarousel(owlCarouselOptions)
  },
}
</script>
<style lang="scss">
.owl-carousel .owl-item img {
  display: inline;
  width: auto;
}
.latest-results__micro-flag {
  width: 17px !important;
}

.carousel-right,
.carousel-left {
  position: absolute;
  left: -50px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 35px;
  z-index: 10;
  text-shadow: 0 0 1px rgba(255,255,255,.8);
}

.carousel-right {
  left: auto;
  right: -50px;
}

@media all and (max-width: 1000px) {
  .carousel-right {
    right:15px;
  }
  .carousel-left {
    left: 15px;
  }
}

</style>
