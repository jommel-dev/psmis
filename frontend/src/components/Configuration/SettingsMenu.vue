<template>
    <div>
      <q-item
        clickable
        tag="a"
        :key="title"
        @click="selectMenu(link)"
        v-if="children.length === 0"
        style="padding: 0px !important;"
      > 
        <q-item-section
          v-if="icon"
          avatar
        >
          <q-avatar 
            rounded 
            :class="selectedMenu === link ? 'generatedIconDash' : ''"
            :text-color="selectedMenu === link ? 'white' : 'grey-10'" 
            :icon="icon" 
            size="xl" 
          />
        </q-item-section>

        <q-item-section
          class="q-pt-sm q-pb-sm q-pr-sm"
        > 
          <q-item-label :class="selectedMenu === link ? 'text-bold text-grey-9' : 'text-grey'"  >{{ title }}</q-item-label>
        </q-item-section>
      </q-item>
    </div>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'SideNavigation',
    props: {
        selectedMenu:{
            type: String,
            default: "userRoles"
        },

        title: {
            type: String,
            required: true
        },

        link: {
            type: String,
            default: '#'
        },

        icon: {
            type: String,
            default: ''
        },

        children: {
          type: Array,
          default: []
        }
    },
    methods:{
        selectMenu(selected){
            this.$emit("menuClicked", selected);
        }
    }
})
</script>

<style scoped>
.bordered-no-right{
  border: 1px solid #80808061;
  border-right: none;
  border-radius: 10px 0px 0px 10px;
}
.bordered-no-left{
  border: 1px solid #80808061;
  border-left: none;
  border-radius: 0px 10px 10px 0px;
}
.inactiveMenuState{
  background: white;
}

.activeItemClass{
  background: white;
  border: 1px solid #80808061;
  border-radius: 10px;
}
.generatedIconDash{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(250 235 0) 0%, rgb(40 148 255) 94%);
}
</style>
