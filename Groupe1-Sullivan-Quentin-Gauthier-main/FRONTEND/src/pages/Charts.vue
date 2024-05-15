<template>
  <q-page class="q-pa-sm">
    <div class="row q-col-gutter-sm q-py-sm">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <bar-chart></bar-chart>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <line-chart></line-chart>
      </div>
      <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
        <TemperatureChart></TemperatureChart>
      </div>
    </div>
  </q-page>
</template>

<script>
import {defineComponent, defineAsyncComponent} from 'vue'

export default defineComponent({
  name: "Charts",
  components: {
    LineChart: defineAsyncComponent(() => import('components/charts/LineChart.vue')),
    BarChart: defineAsyncComponent(() => import('components/charts/BarChart.vue')),
    TemperatureChart: defineAsyncComponent(() => import('components/charts/TemperatureChart.vue'))
  },
})
</script>

<style scoped>

</style>
