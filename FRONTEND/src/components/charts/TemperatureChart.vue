<template>
  <div>
    <q-card class="no-shadow" bordered>
      <q-card-section class="text-h6">
        Relevé température de la journée °C
        <q-btn icon="fa fa-download" class="float-right" @click="SaveImage" flat dense>
          <q-tooltip>Télécharger relevé</q-tooltip>
        </q-btn>
      </q-card-section>
      <q-card-section>
        <ECharts ref="TemperatureChart"
                 :option="option"
                 class="q-mt-md"
                 :resizable="true"
                 autoresize style="height: 300px;"
        />
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import ECharts from "vue-echarts";
import {defineComponent} from "vue";
import "echarts";
export default defineComponent(
  {
  name: "TemperatureChart",
  components:{
    ECharts
  },
  methods: {
    SaveImage() {
      const linkSource = this.$refs.TemperatureChart.getDataURL();
      const downloadLink = document.createElement('a');
      document.body.appendChild(downloadLink);
      downloadLink.href = linkSource;
      downloadLink.target = '_self';
      downloadLink.download = 'ReleveTemperatureQuotidien.png';
      downloadLink.click();
    },
  },
  data(){
    return {
      option :{
  xAxis: {
    data: ['0h','1h','2h','3h','4h','5h','6h','7h','8h','9h','10h','11h','12h','13h','14h','15h','16h','18h','19h','20h','21h','22h','23h',]
  },
  yAxis: {},
  series: [
        {
          data: [10, 22, 28, 23, 19,10, 22, 28, 23, 19, 17, 10, -7, -6, -2, 5, 6, 8, 13, 28, 23, 19, 17, 10],
          type: 'line',
          smooth:true,
          label: {
            show: true,
            position: 'bottom',
            textStyle: {
              fontSize: 20
            }
          }
        }
      ]
    }
  }
}
}
)
</script>

<style>

</style>
