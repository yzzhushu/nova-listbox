<template>
    <div :class="`text-${field.textAlign}`">
        <div v-if="field.value.length > 0">
            <Tooltip
                :triggers="['hover']"
                :popperTriggers="['hover']"
                placement="top"
                theme="plain"
                @show="loadLists"
                :auto-hide="true"
                distance="6"
            >
                <template #default>
                    <span class="link-default">{{ field.displayedAs || field.value.length }}</span>
                </template>
                <template #content>
                    <HxTable
                        v-if="columns.length > 0"
                        :columns="columns"
                        :lists="lists"
                        class="min-w-[24rem] max-w-2xl"
                        style="max-height: calc(360px + .5rem)"
                    />
                    <ul
                        v-else
                        style="max-height: calc(360px + .5rem);overflow: scroll;"
                        class="py-3 min-w-[24rem] max-w-2xl"
                    >
                        <li v-for="item in lists"
                            style="line-height: 40px;"
                            class="cursor-pointer hx-list-column text-center h-10 px-6">
                            {{ item.name }}
                        </li>
                    </ul>
                </template>
            </Tooltip>
        </div>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
import request from "../request";
export default {
    mixins: [request],

    mounted() {
        if (typeof this.field.options !== 'string') {
            this.loading = true;
            this.handleLists(this.field.options);
        } else if (this.field.belongsToMany) {
            this.loading = true;
            this.formatLists(this.field.value);
        } else {
            if (this.field.value.length === 0) return;
            this.lists = this.field.value.map(item => {
                return {code: item, name: ''}
            });
        }
    },

    computed: {
        columns() {
            return this.field.columns ?? [];
        }
    }
}
</script>
