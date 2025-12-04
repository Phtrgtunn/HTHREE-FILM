// generateMoviesJson.js (Chạy bằng Node.js)
// Script này sẽ fetch dữ liệu phim từ phimapi.com và lưu vào một file movies.json
// KHÔNG CẦN CÀI ĐẶT supabase/supabase-js và dotenv nữa, nhưng cần axios và fs.

// --- BƯỚC 1: ĐẢM BẢO CÁC GÓI ĐƯỢC CÀI ĐẶT ---
// Mở terminal trong thư mục gốc của dự án và chạy:
// npm install axios fs-extra
import axios from 'axios';
import fs from 'fs/promises'; // Sử dụng fs/promises cho các thao tác bất đồng bộ
import path from 'path';

// --- KHAI BÁO BIẾN CẦN THIẾT ---
const OUTPUT_FILE = path.resolve(process.cwd(), 'movies.json');
const allMoviesData = []; // Mảng chứa tất cả dữ liệu phim đã được chuẩn hóa

// Hàm này sẽ chuẩn hóa URL ảnh, tránh việc ghép nối tên miền hai lần
function normalizeImageUrl(imageUrl, cdnDomain) {
    if (!imageUrl) return null;
    // Kiểm tra xem URL đã chứa tên miền CDN chưa
    if (imageUrl.startsWith(cdnDomain)) {
        return imageUrl; // Nếu đã có, trả về nguyên bản
    }
    // Nếu chưa, ghép nối tên miền CDN
    return `${cdnDomain}/${imageUrl.startsWith('/') ? imageUrl.substring(1) : imageUrl}`;
}

async function fetchDataAndNormalize(apiUrl, categoryLabel, pageLimit) {
    console.log(`Bắt đầu quá trình tải và chuẩn hóa dữ liệu ${categoryLabel} từ ${apiUrl}...`);

    for (let page = 1; page <= pageLimit; page++) {
        try {
            console.log(`Đang lấy dữ liệu từ trang ${page} của ${apiUrl}...`);
            const response = await axios.get(`${apiUrl}?page=${page}`);
            
            let apiData;
            if (response.data && response.data.data) {
                apiData = response.data.data; 
            } else if (response.data && response.data.items) {
                apiData = response.data; 
            } else {
                console.error(`Cấu trúc dữ liệu API không mong muốn từ ${apiUrl}. Bỏ qua trang này.`, response.data);
                continue; 
            }

            const moviesFromApi = apiData.items;
            const IMAGE_CDN_DOMAIN = apiData.APP_DOMAIN_CDN_IMAGE || 'https://phimimg.com';

            if (!moviesFromApi || moviesFromApi.length === 0) {
                console.log(`Không tìm thấy phim ${categoryLabel} ở trang ${page}. Dừng lại.`);
                break; 
            }

            const normalizedMovies = moviesFromApi.map(movie => {
                const runtimeMatch = movie.time ? movie.time.match(/(\d+)\s*phút/) : null;
                const runtimeMinutes = runtimeMatch ? parseInt(runtimeMatch[1]) : null;

                const fullPosterPath = normalizeImageUrl(movie.thumb_url, IMAGE_CDN_DOMAIN);
                const fullBackdropPath = normalizeImageUrl(movie.poster_url, IMAGE_CDN_DOMAIN);

                return {
                    api_id: movie._id,
                    title: movie.name,
                    original_title: movie.origin_name || null,
                    overview: movie.content || null,
                    poster_path: fullPosterPath, 
                    backdrop_path: fullBackdropPath, 
                    release_date: movie.year ? `${movie.year}-01-01` : null, 
                    vote_average: movie.tmdb?.vote_average ? parseFloat(movie.tmdb.vote_average) : 0,
                    runtime: runtimeMinutes,
                    tagline: movie.chieurap ? 'Phim Chiếu Rạp' : null,
                    // Lưu dưới dạng mảng (MySQL JSON)
                    genres: movie.category ? movie.category.map(cat => cat.name) : [], 
                    country: movie.country ? movie.country.map(c => c.name) : [], 
                    type: movie.type || null, 
                    slug: movie.slug || null, 
                    year: movie.year || null,
                    episode_current: movie.episode_current || null,
                    episode_total: movie.episode_total || null,
                    quality: movie.quality || null,
                    lang: movie.lang || null,
                    actor: movie.actor || [], 
                    director: movie.director || [], 
                    view: movie.view || 0,
                    status: movie.status || null,
                    is_copyright: movie.is_copyright || false,
                    sub_docquyen: movie.sub_docquyen || false,
                    chieurap: movie.chieurap || false,
                    trailer_url: movie.trailer_url || null,
                    notify: movie.notify || null,
                    showtimes: movie.showtimes || null,
                };
            });
            
            // Thêm vào mảng tổng
            allMoviesData.push(...normalizedMovies);
            console.log(`Tìm thấy và chuẩn hóa ${normalizedMovies.length} phim ${categoryLabel} ở trang ${page}.`);

            await new Promise(resolve => setTimeout(resolve, 500)); 
        } catch (apiError) {
            console.error(`Lỗi khi gọi API phim ${categoryLabel} ở trang ${page}:`, apiError.message); 
            break; 
        }
    }
    console.log(`Hoàn thành quá trình tải và chuẩn hóa dữ liệu ${categoryLabel}.`);
}

// --- HÀM THÊM PHIM ĐƠN LẺ VÀO MẢNG TỔNG ---
function addSingleMovieToAllMovies(movieData) {
    console.log("Bắt đầu chuẩn hóa phim đơn lẻ...");
    const IMAGE_CDN_DOMAIN_SINGLE = 'https://phimimg.com'; 

    const runtimeMatch = movieData.time ? movieData.time.match(/(\d+)/) : null;
    const runtimeMinutes = runtimeMatch ? parseInt(runtimeMatch[1]) : null;

    const movieToInsert = {
        api_id: movieData._id,
        title: movieData.name,
        original_title: movieData.origin_name || null,
        overview: movieData.content || null,
        poster_path: normalizeImageUrl(movieData.thumb_url, IMAGE_CDN_DOMAIN_SINGLE), 
        backdrop_path: normalizeImageUrl(movieData.poster_url, IMAGE_CDN_DOMAIN_SINGLE),
        release_date: movieData.year ? `${movieData.year}-01-01` : null, 
        vote_average: movieData.tmdb?.vote_average ? parseFloat(movieData.tmdb.vote_average) : 0,
        runtime: runtimeMinutes, 
        tagline: movieData.tagline || movieData.chieurap ? 'Phim Chiếu Rạp' : null, 
        genres: movieData.category ? movieData.category.map(cat => cat.name) : [],
        country: movieData.country ? movieData.country.map(c => c.name) : [], 
        type: movieData.type || null,
        slug: movieData.slug || null,
        year: movieData.year || null,
        episode_current: movieData.episode_current || null,
        episode_total: movieData.episode_total || null,
        quality: movieData.quality || null,
        lang: movieData.lang || null,
        actor: movieData.actor || [],
        director: movieData.director || [],
        view: movieData.view || 0,
        status: movieData.status || null,
        is_copyright: movieData.is_copyright || false,
        sub_docquyen: movieData.sub_docquyen || false,
        chieurap: movieData.chieurap || false,
        trailer_url: movieData.trailer_url || null,
        notify: movieData.notify || null,
        showtimes: movieData.showtimes || null,
    };

    allMoviesData.push(movieToInsert);
    console.log("Phim đơn lẻ đã được chuẩn hóa và thêm vào mảng tổng.");
}

// Hàm chính để gọi các hàm tải dữ liệu và lưu vào file
async function main() {
    // 1. Nhập phim bộ (5 trang)
    await fetchDataAndNormalize('https://phimapi.com/v1/api/danh-sach/phim-bo', 'phim bộ', 5);
    console.log("\n-----------------------------------\n"); 
    
    // 2. Nhập phim lẻ (5 trang)
    await fetchDataAndNormalize('https://phimapi.com/v1/api/danh-sach/phim-le', 'phim lẻ', 5);
    console.log("\n-----------------------------------\n"); 
    
    // 3. Nhập phim mới cập nhật (5 trang)
    await fetchDataAndNormalize('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=${page}', 'phim mới cập nhật', 5);
    console.log("\n-----------------------------------\n");

    // 4. Thêm phim đơn lẻ
    const singleMovieData = {
        "status": true,
        "msg": "",
        "movie": {
          "tmdb": { "type": "movie", "id": "1306845", "season": null, "vote_average": 5.1, "vote_count": 8 },
          "imdb": { "id": null },
          "created": { "time": "2025-06-08T21:33:53.000Z" },
          "modified": { "time": "2025-06-09T02:13:43.000Z" },
          "_id": "f4d3d2d59003d83fb3ef32f78610b4a2",
          "name": "Người Bảo Vệ Công Lý",
          "slug": "nguoi-bao-ve-cong-ly",
          "origin_name": "Jaat",
          "content": "Phim xoay quanh cuộc chiến giữa thiện và ác. Một bên là Ranatunga: tội phạm khét tiếng, tàn nhẫn, lợi dụng quyền lực và vàng để đàn áp dân lành. Bên kia là “Jaat”, vị khách bí ẩn sắt đá, đến để đòi một lời xin lỗi và đem lại hy vọng cho người dân bị áp bức.",
          "type": "single",
          "status": "completed",
          "poster_url": "https://phimimg.com/upload/vod/20250608-1/76ba9b77011ea542890bc8d7667ecfdc.jpg",
          "thumb_url": "https://phimimg.com/upload/vod/20250608-1/429243f5b2d2e4674366cd26318168ef.jpg",
          "is_copyright": false,
          "sub_docquyen": false,
          "chieurap": false,
          "trailer_url": "https://www.youtube.com/watch?v=7noiElC2MpE",
          "time": "153 phút",
          "episode_current": "Full",
          "episode_total": "1",
          "quality": "FHD",
          "lang": "Vietsub",
          "notify": "",
          "showtimes": "",
          "year": 2025,
          "view": 0,
          "actor": [ "Sunny Deol", "Randeep Hooda", "Saiyami Kher", "Regina Cassandra", "Vineet Kumar Singh", "Ramya Krishnan", "Jagapati Babu", "Vinay Varma", "Zarina Wahab", "Upendra Limaye", "Urvashi Rautela", "Inaya Sultana", "Divi Vadthya", "Bandhavi Sridhar", "Ayesha Khan", "Makrand Deshpande", "Pranee" ],
          "director": [ "Gopichand Malineni" ],
          "category": [ { "id": "9822be111d2ccc29c7172c78b8af8ff5", "name": "Hành Động", "slug": "hanh-dong" }, { "id": "7a035ac0b37f5854f0f6979260899c90", "name": "Hình Sự", "slug": "hinh-su" }, { "id": "37a7b38b6184a5ebd3c43015aa20709d", "name": "Chính Kịch", "slug": "chinh-kich" } ],
          "country": [ { "id": "aadd510492662beef1a980624b26c685", "name": "Ấn Độ", "slug": "an-do" } ]
        },
        "episodes": [ { "server_name": "#Hà Nội (Vietsub)", "server_data": [ { "name": "Full", "slug": "full", "filename": "Người Bảo Vệ Công Lý - Jaat - 2025 - 1080p - Vietsub - Full", "link_embed": "https://player.phimapi.com/player/?url=https://s6.kkphimplayer6.com/20250608/ASUwKkwG/index.m3u8", "link_m3u8": "https://s6.kkphimplayer6.com/20250608/ASUwKkwG/index.m3u8" } ] } ]
    };
    addSingleMovieToAllMovies(singleMovieData.movie); 

    // 5. Lưu tất cả dữ liệu đã chuẩn hóa vào file JSON
    console.log(`\n-----------------------------------`);
    console.log(`Tổng cộng đã thu thập ${allMoviesData.length} phim. Đang lưu vào ${OUTPUT_FILE}...`);

    try {
        await fs.writeFile(OUTPUT_FILE, JSON.stringify(allMoviesData, null, 2), 'utf8');
        console.log(`THÀNH CÔNG: Dữ liệu đã được lưu vào ${OUTPUT_FILE}.`);
        console.log("Bây giờ bạn có thể dùng file này để tạo Seeder cho Laravel/MySQL.");
    } catch (err) {
        console.error(`LỖI: Không thể lưu file JSON.`, err);
    }
}

main();