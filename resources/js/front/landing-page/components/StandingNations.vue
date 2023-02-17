<template>
    <div>
      <h2 class="h1 font-weight font-weight-bold text-blue text-uppercase mb-4">{{__('Standing nations')}}</h2>
      <div v-if="loading">{{__('Loading rankings')}}...</div>
        <div v-else-if="error" class="alert alert-danger">
            {{error}}
        </div>
        <div v-else>
            <div class="table-responsive">
                <table class="table table--races text-uppercase">
                    <thead>
                        <tr>
                          <th style="width:30px;" class="pl-0">{{__('Rnk')}}</th>
                            <th>{{__('Nation')}}</th>
                            <th style="width: 80px;" class="text-right">{{__('Points')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(entry, rank) in item.ranking"
                            :key="`cr-${entry.code}-${rank}`"
                        >
                            <td class="pl-0 text-nowrap"><b>{{ rank+1 }}.</b></td>
                            <td class="text-nowrap">
                                <a :href="`/athletes/?country=${entry.code}#athlete-list`" class="d-inline-flex align-items-center">
                                    <flag :code="entry.code" class="mr-1" />
                                    <div class="font-weight-bold">
                                        {{ entry.name }}
                                    </div>
                                </a>
                            </td>
                            <td class="text-right text-nowrap"><b>{{ entry.pts }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
import Flag from '../../shared/components/Flag'

const RANKING_URL = '/rankings/homepage/nations'

export default {
    components: {
        Flag,
    },

    data() {
        return {
            items: [],
            item: null,
            loading: true,
            error: '',
        }
    },

    async created() {
        try {
            await this.getResults()
        } catch(e) {
            this.error = e.message
            this.loading = false
        }
    },

    methods: {
        async getResults() {
            const { data: result } = await axios.post(RANKING_URL)
            if (result && result.length) {
                this.items = result
                this.item = result[0]
                this.loading = false
            } else {
                throw new Error('No ranking available.')
            }
        },

        selectItem(item) {
            this.item = item
        },
    },
}
</script>
