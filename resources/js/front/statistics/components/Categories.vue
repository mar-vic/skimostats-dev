<template>
  <div>
    <div class="font-weight-bold text-blue">{{__('Select your category')}}:</div>
    <div class="row mb-5 pb-2 pt-2" style="border-bottom:2px dotted; border-top:2px dotted;">

      <!-- FIRST MENU COLUMN -->
      <div class="col mt-2 mb-2">
        <ul class="navbar-nav">
          <li class="nav-item" :class="{ selected: category.isSelected }" v-for="category in statsCategories.slice(0, 5)" :key="category.id">
            <a @click.prevent="changeStatsCategory($event, category.id)" class="nav-link pl-3" href="#">{{category.longName}}</a>
          </li>
        </ul>
      </div>

      <!-- SECOND MENU COLUMN -->
      <div class="col mt-2 mb-2">
        <ul class="navbar-nav">
          <li class="nav-item" :class="{ selected: category.isSelected }" v-for="category in statsCategories.slice(5, 10)" :key="category.id">
            <a  @click.prevent="changeStatsCategory($event, category.id)" class="nav-link pl-3" href="#">{{category.longName}}</a>
          </li>
        </ul>
      </div>

      <!-- THIRD MENU COLUMN -->
      <div class="col mt-2 mb-2">
        <ul class="navbar-nav">
          <li class="nav-item"  :class="{ selected: category.isSelected }" v-for="category in statsCategories.slice(10, 15)" :key="category.id">
            <a @click.prevent="changeStatsCategory($event, category.id)" class="nav-link pl-3" href="#">{{category.longName}}</a>
          </li>
        </ul>
      </div>

      <!-- FOURTH MENU COLUMN -->
      <div class="col mt-2 mb-2">
        <ul class="navbar-nav">
          <li class="nav-item" :class="{ selected: category.isSelected }" v-for="category in statsCategories.slice(15, 17)" :key="category.id">
            <a @click.prevent="changeStatsCategory($event, category.id)" class="nav-link pl-3" href="#">{{category.longName}}</a>
          </li>
        </ul>
      </div>

    </div>

  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  // components: {
  //   AthleteRaces,
  //   CareerWins,
  //   RankingStrip
  // }
  computed: {
    ...mapState(['statsCategories'])
  },

  methods: {
    changeStatsCategory (event, categoryId) {
      this.$store.dispatch('selectStatsCategory', categoryId)
    }
  }
}
</script>

<style scoped>
.nav-item:not(.selected) > a::before {
  content: '';
  display: block;
  background: #fce000;
  width: 0%;
  height: 5px;
  position: relative;
  top: 30px;
  left: 80px;
  /* left: 50%; */
  /* bottom: -100px; */
  border-top-left-radius:15px;
  border-top-right-radius:15px;
  transform: translateX(-50%);
  transition:.2s all ease;
}

.nav-item:not(.selected) > a:hover::before  {
  bottom: 0;
  width: 80%;
  border-radius: 0;
}

.nav-item.selected {
  background-color: #fce000;
  border-bottom-right-radius: 25px;
  font-weight: bold;
}
</style>
