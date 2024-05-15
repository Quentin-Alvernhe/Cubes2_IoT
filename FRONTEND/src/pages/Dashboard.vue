<template>
  <q-page class="q-pa-sm">
    <div class="row q-col-gutter-sm  q-py-sm">
      <card-with-image/>
      <div class="column ">
        <q-btn round color="green" size="xs"><q-tooltip>Sonde connectée</q-tooltip></q-btn>
        <q-btn round color="red" size="xs" icon="delete"><q-tooltip>Supprimer la sonde</q-tooltip></q-btn>
      </div>
    </div>
    <div class="q-pa-lg" style="padding-bottom: 0">
    <div>
      <q-fab
        v-model="fab1"
        label="Actions"
        label-position="top"
        external-label
        color="purple"
        icon="keyboard_arrow_right"
        direction="down"
        padding="xs"
      >
        <q-fab-action padding="5px" external-label label-position="right" color="green" @click="onClick" icon="add" label="Ajouter sonde" />
        <q-fab-action padding="5px" external-label label-position="right" color="red" @click="onClick" icon="delete" label="Supprimer sondes" />
      </q-fab>
    </div>
    </div>
  </q-page>
</template>

<script>
import {defineComponent,defineAsyncComponent} from 'vue'

export default defineComponent({
  name: 'PageIndex',
  components: {
    CardWithImage: defineAsyncComponent(() => import('components/cards/CardWithImage.vue')),
  },
})
</script>
