<template>
    <div :class="`text-${field.textAlign}`">
        <Dropdown v-if="field.value.length > 0">
            <DropdownTrigger
                class="text-gray-500 inline-flex items-center cursor-pointer"
                :showArrow="false"
            >
                <span class="link-default font-bold">{{ display }}</span>
            </DropdownTrigger>

            <template #menu>
                <DropdownMenu width="auto">
                    <ul
                        style="width: auto;max-width: 320px;max-height: 232px;overflow: scroll;"
                        class="py-3"
                    >
                        <li v-for="tag in tags"
                            style="height: 40px;line-height: 40px;"
                            class="cursor-pointer list-box-item text-center px-6">
                            {{ tag.name }}
                        </li>
                    </ul>
                </DropdownMenu>
            </template>
        </Dropdown>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            tags: [],
            display: ''
        }
    },

    mounted() {
        this.display = this.field.displayedAs || this.field.value.length;

        let tags = this.field.value;
        let list = [];
        for (let i = 0; i < tags.length; i++) {
            list.push({
                code: tags[i].id,
                name: (this.field.nameWithCode ? (tags[i].id + ' - ') : '') + tags[i].name
            })
        }
        this.tags = list;
    },
}
</script>
