<div x-data="{
    name: '',
    uploading: false,
    url: '{{ $lesson->cover_url['large'] }}',
    progress: 0,
    get message() {
        return (this.progress < 100) ? `SEDANG DIUNGGAH: ${this.progress}%` : 'GAMBAR SEDANG DIPROSES'
    },
    async onChange(e) {
        this.uploading = true
        const onUploadProgress = (e) => {
            this.progress = Math.round((e.loaded * 100) / e.total);
        }
        try {
            const [file] = e.target.files
            if (file) {
                const formData = new FormData();
                formData.append('image', file);
                this.name = file ? file.name : ''
                const { data } = await window.axios.post('{{ route('dashboard.lessons.update.cover', ['lesson' => $lesson->id]) }}', formData, { onUploadProgress });
                this.url = data.urls.large
                window.fire.success(data.message)
            }
        } catch (err) {
            window.fire.error(e.response?.data.message || e.message)
        }
        this.uploading = false
    },
    onComplete() {
        this.name = ''
        this.uploading = false
        this.progress = 0
    }
}">
  <div class="p-3">
    <div class="md:flex">
      <img x-bind:src="url" class="block md:w-80" />
      <div class="flex min-h-[100px] flex-1 flex-col md:pl-3">
        <div class="relative flex flex-1 flex-col border border-dashed bg-gray-50 p-3">
          <p class="m-auto text-center text-sm leading-none" x-text="name || 'KLIK DISINI ATAU SERET GAMBAR KESINI'"></p>
          <p class="text-center text-sm" x-show="uploading" x-text="message"></p>
          <input x-bind:disabled="uploading" type="file" aria-hidden="true" accept="image/*" x-on:change="onChange"
            class="absolute z-10 block h-full w-full opacity-0" />
        </div>
      </div>
    </div>
  </div>
</div>
