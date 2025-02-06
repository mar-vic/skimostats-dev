<template>
    <div class="latest-results" v-if="activeEvent">
        <div>
            <div class="latest-results__container">
                <div class="latest-results__left__container">
                    <div class="latest-results__left">
                        <div class="latest-results__left-text font-family-heading">
                            {{__('Latest')}}
                            <div>{{__('Results')}}</div>
                        </div>
                    </div>
                </div>
                <div class="latest-results__middle">
                    <h1 class="text-uppercase font-weight-bold mb-0">
                        <span class="d-block float-none float-md-right">{}</span>
                    </h1>
                    <h1 class
                </div>
                <div class="latest-results__right__container">
                    <div class="latest-results__right">
                        <div class="latest-results__right-pagination mb-4 mb-md-0">
                            <a href="#" class="latest-results__prev-race" @click.prevent="changeEvent('prev')">{{__('Previous')}}<span class='d-none d-md-inline'> {{__('race')}}</span></a>
                            <span class="h1 my-md-3 my-0 mx-3 mx-lg-0 font-weight-bold">{{ String(activeEventPosition).padStart(2, '0') }}/{{ String(eventCount).padStart(2, '0') }}</span>
                            <a href="#" class="latest-results__next-race" @click.prevent="changeEvent('next')">{{__('Next')}}<span class='d-none d-md-inline'> {{__('race')}}</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
</script>
