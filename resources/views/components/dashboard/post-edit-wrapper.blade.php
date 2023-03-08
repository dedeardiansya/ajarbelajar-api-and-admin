@props(['post', 'type' => 'lesson'])

<div class="container p-3">
  <div class="mb-3 rounded border-b-4 border-primary-500 bg-white shadow">
    <div class="flex items-center border-b px-3 py-5">
      <h3 class="flex-1 text-xl font-semibold">Edit {{ $type == 'lesson' ? 'Pelajaran' : 'Artikel' }}</h3>
      <form x-data="{
          loading: false,
          onclick(e) {
              e.preventDefault()
              this.loading = true
              this.$store.deleteConfirm(() => {
                  this.$refs.formDeletePost.requestSubmit()
              }, () => {
                  this.loading = false
              }, {
                  text: 'Kamu akan menghapus pelajaran ini secara permanen!',
              })
          }
      }" x-ref="formDeletePost" method="POST" action="{{ route('dashboard.' . $type . 's.destroy', $post->id) }}">
        @csrf
        @method('DELETE')
        <x-button type="button" @click="onclick" x-bind:disabled="loading" variant="red"
          class="!block w-full h-full !bg-red-100 !text-red-900 hover:!bg-red-600 disabled:!bg-red-600 hover:!text-white !border-0 disabled:!text-transparent">
          Hapus</x-button>
      </form>
    </div>
    <div class="flex flex-col md:flex-row border-b">
      <div class="p-3 relative md:w-52">
        @if ($post->public)
          <span class="bg-primary absolute top-2 left-2 text-xs  text-white shadow px-2 py-1 rounded bg-primary-600">PUBLIK</span>
        @endif
        <img class="block w-full rounded" src="{{ $post->cover_url['thumb'] }}" alt="Gambar dari pelajaran: {{ $post->title }}" />
      </div>
      <div class="p-3 pt-0 md:pl-0 md:pt-3 flex-1">
        <div class="mb-2">
          <p class="fort-semibold text-xs">Diperbarui {{ $post->updated_at->diffForHumans() }}</p>
          @if ($post->category)
            <span class="text-2xs font-semibold bg-gray-100 border px-2 py-1 rounded-sm">{{ $post->category->name }}</span>
          @endif
        </div>
        <h3 class="font-semibold">{{ $post->title }}</h3>
        <p class="text-xs">Dibuat pada {{ $post->created_at->translatedFormat('l, d F Y') }}</p>
      </div>
    </div>

    {{ $slot }}

  </div>
</div>
