<div class="form-group">
    <label for="title" class="form-label">Judul Buku</label>
    <input
        type="text"
        id="title"
        name="title"
        value="{{ old('title', $book->title ?? '') }}"
        class="form-control"
        required
        placeholder="Contoh: Laskar Pelangi"
    >
    @error('title')
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="author" class="form-label">Penulis</label>
    <input
        type="text"
        id="author"
        name="author"
        value="{{ old('author', $book->author ?? '') }}"
        class="form-control"
        required
        placeholder="Contoh: Andrea Hirata"
    >
    @error('author')
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="publisher" class="form-label">Penerbit</label>
    <input
        type="text"
        id="publisher"
        name="publisher"
        value="{{ old('publisher', $book->publisher ?? '') }}"
        class="form-control"
        required
        placeholder="Contoh: Bentang Pustaka"
    >
    @error('publisher')
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
    <div class="form-group">
        <label for="year" class="form-label">Tahun Terbit</label>
        <input
            type="number"
            id="year"
            name="year"
            value="{{ old('year', $book->year ?? date('Y')) }}"
            class="form-control"
            min="1900"
            max="{{ date('Y') + 1 }}"
            required
        >
        @error('year')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="stock" class="form-label">Jumlah Stok</label>
        <input
            type="number"
            id="stock"
            name="stock"
            value="{{ old('stock', $book->stock ?? 1) }}"
            class="form-control"
            min="0"
            required
        >
        @error('stock')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>
</div>
