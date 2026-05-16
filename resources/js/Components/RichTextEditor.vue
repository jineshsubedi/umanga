<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { watch, onBeforeUnmount } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Write here...' },
});
const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [StarterKit],
    editorProps: {
        attributes: {
            class: 'prose max-w-none min-h-[200px] px-4 py-3 focus:outline-none text-sm text-gray-800',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (value) => {
    if (editor.value && editor.value.getHTML() !== value) {
        editor.value.commands.setContent(value || '', false);
    }
});

onBeforeUnmount(() => editor.value?.destroy());

const btn = (action, icon) => ({ action, icon });

const toolbarActions = [
    { cmd: 'toggleBold',   icon: '<b>B</b>',  title: 'Bold' },
    { cmd: 'toggleItalic', icon: '<i>I</i>',  title: 'Italic' },
    { cmd: 'toggleStrike', icon: '<s>S</s>',  title: 'Strikethrough' },
    { cmd: 'toggleBulletList', icon: '• List', title: 'Bullet List' },
    { cmd: 'toggleOrderedList', icon: '1. List', title: 'Ordered List' },
];

const run = (cmd) => editor.value?.chain().focus()[cmd]().run();
</script>

<template>
    <div class="border border-gray-300 rounded-md overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
        <!-- Toolbar -->
        <div class="flex flex-wrap gap-1 px-3 py-2 bg-gray-50 border-b border-gray-200">
            <button v-for="action in toolbarActions" :key="action.cmd"
                type="button"
                @click="run(action.cmd)"
                :title="action.title"
                class="px-2 py-1 text-xs rounded hover:bg-gray-200 transition-colors text-gray-700 font-medium"
                v-html="action.icon">
            </button>
            <div class="w-px bg-gray-200 mx-1"></div>
            <button type="button" @click="editor?.chain().focus().undo().run()" title="Undo"
                class="px-2 py-1 text-xs rounded hover:bg-gray-200 transition-colors text-gray-600">↩ Undo</button>
            <button type="button" @click="editor?.chain().focus().redo().run()" title="Redo"
                class="px-2 py-1 text-xs rounded hover:bg-gray-200 transition-colors text-gray-600">↪ Redo</button>
        </div>
        <!-- Editor -->
        <EditorContent :editor="editor" />
        <div v-if="!editor?.getText()" class="px-4 py-3 text-sm text-gray-400 absolute pointer-events-none -mt-9">
        </div>
    </div>
</template>

<style scoped>
:deep(.ProseMirror) { outline: none; }
:deep(.ProseMirror ul) { list-style-type: disc; padding-left: 1.2em; }
:deep(.ProseMirror ol) { list-style-type: decimal; padding-left: 1.2em; }
:deep(.ProseMirror strong) { font-weight: 700; }
:deep(.ProseMirror em) { font-style: italic; }
:deep(.ProseMirror s) { text-decoration: line-through; }
:deep(.ProseMirror p) { margin: 0 0 0.5em 0; }
</style>
