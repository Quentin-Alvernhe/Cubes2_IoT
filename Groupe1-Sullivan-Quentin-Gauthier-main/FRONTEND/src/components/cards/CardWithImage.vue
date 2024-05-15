<template>
  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
    <q-card class="my-card no-shadow" bordered>
      <q-img
        src="../../assets/Nancy.jpeg"
        basic
      >
        <div class="absolute-bottom-left bg-transparent q-ml-md">
          <div class="text-h4">
            {{ temps.main.temp }} °C
          </div>
          <div class="text-h5">
            {{ temps.name }}
          </div>
          <div class="text-h6">
            pression: {{ temps.main.pressure }} hPa
          </div>
          <div class="text-h6">
            humidité: {{ temps.main.humidity }} %
          </div>
          <div class="text-h6">
            {{ temps.weather[0].description }}
          </div>
        </div>
      </q-img>
    </q-card>
    <div>
      <input type="text" id="position" v-model="requete" v-on:keypress="goMeteo">
    </div>
  </div>

</template>

<script>
import {defineComponent} from 'vue'
import axios from 'axios'
export default defineComponent({
  name: 'CardWithImage',
  data(){
    return{
      requete: '',
      temps: undefined,
      api_code: '153495f91f83fe64162fd888f243e482',
    }
  },
  methods: {
    goMeteo(e){
      if (e.key == "Enter"){
        axios
          .get(`https://api.openweathermap.org/data/2.5/weather?q=${this.requete}&units=metric&appid=${this.api_code}&lang=fr`)
          .then(reponse =>{
            this.temps = reponse.data
          })
          this.requete = ''
      }
    }
  }
})
// const {data} = await axios.post('151.80.61.248:49154', {
//     sondes:"Texte",
//     action:"readMesures"
//   },
//)
</script>
