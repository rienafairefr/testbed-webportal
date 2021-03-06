<template>
  <div>
    <router-link :to="{name: 'newFirmware'}" class="btn btn-sm btn-outline-success float-right"><i class="fa fa-plus"></i> New firmware</router-link>
    <ul class="nav nav-tabs" style="position: relative; top: 1px">
      <li class="nav-item" v-tooltip:top="'User firmwares'">
        <a class="nav-link active" data-toggle="list" href="#userdefined" role="tab" aria-controls="userdefined" @click="filterType = 'userdefined'"> My firmwares </a>
      </li>
      <li class="nav-item" v-tooltip:top="'Predefined firmwares'">
        <a class="nav-link" data-toggle="list" href="#predefined" role="tab" aria-controls="predefined" @click="filterType = 'predefined'"> Presets </a>
      </li>
    </ul>
    <table class="table table-striped table-sm">
      <thead>
        <tr>
          <th class="cursor" title="sort by identifier" @click="sortBy(f => f.name)">Identifier</th>
          <th class="cursor" title="sort by archi" @click="sortBy(f => f.archi || '')">Archi</th>
          <th class="cursor" title="sort by OS" @click="sortBy(f => f.os || '')">OS</th>
          <th class="cursor" title="sort by description" @click="sortBy(f => f.description || '')">Description</th>
          <th width="15"><i class="fa fa-download" v-tooltip:bottom="'Download'"></i></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="firmware in store.firmwares" v-if="filter(firmware)">
          <td>
            <a v-if="select" href="#" @click.prevent="selectItem(firmware)">{{firmware.name}}</a>
            <router-link v-else :to="{name: 'firmware', params: {name: firmware.name}}">
              {{firmware.name}}
            </router-link>
          </td>
          <td>{{firmware.archi}}</td>
          <td>{{firmware.os}}</td>
          <td class="ellipsis" :title="firmware.description">{{firmware.description}}</td>
          <td><a href="#" @click.prevent="download(firmware)" v-tooltip:bottom.html="`<i class='fa fa-download'></i> <b>${firmware.filename}</b>`"><i class="fa fa-file-o"></i></a></td>
        </tr>
        <tr v-if="store.firmwares.length === 0">
          <td colspan="4" class="font-italic bg-light">
            <router-link :to="{name: 'newFirmware'}">upload a firmware</router-link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { iotlab } from '@/rest'
import { downloadAsFile } from '@/utils'
import store from '@/store'

export default {
  name: 'FirmwareList',

  props: {
    archi: {
      // Display Firmware filtered by archi(s) (default no filter)
      type: [String, Array],
      default: '',
    },
    select: {
      // true  -> emits a selected event
      // false -> router link to object
      type: Boolean,
      default: false,
    },
  },

  data () {
    return {
      store: store,
      filterType: 'userdefined',
    }
  },

  async created () {
    try {
      store.firmwares = await iotlab.getFirmwares()
      this.sortBy(f => f.archi || 'zzz') // let's put empty archi at the end of the list
    } catch (err) {
      this.$notify({text: 'Failed to load firmwares', type: 'error'})
    }
  },

  methods: {
    sortBy (func) {
      // sort by func() then by firmware name
      store.firmwares = store.firmwares.sort((a, b) => func(a) === func(b) ? a.name.localeCompare(b.name) : func(a).localeCompare(func(b)))
    },
    filter (firmware) {
      if (firmware.type !== this.filterType) {
        return false
      }
      if (this.archi === '') {
        return true
      }
      let archis = (typeof this.archi === 'string') ? Array(this.archi) : this.archi
      return archis.includes(firmware.archi)
    },
    selectItem (firmware) {
      this.$emit('select', firmware.name)
    },
    async download (firmware) {
      try {
        downloadAsFile(firmware.filename, await iotlab.getFirmwareFile(firmware.name))
      } catch (err) {
        this.$notify({text: err.response.data.message || 'Failed to download file', type: 'error'})
      }
    },
  },
}
</script>

<style scoped>
.ellipsis {
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
