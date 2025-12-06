<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Loading State -->
    <div v-if="loading" class="space-y-8">
      <LoadingSkeleton type="hero" />
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">
            <LoadingSkeleton type="list" :count="2" />
          </div>
          <div class="lg:col-span-1">
            <LoadingSkeleton type="card" />
          </div>
        </div>
        <LoadingSkeleton type="list" :count="5" />
      </div>
    </div>

    <!-- Error State -->
    <ErrorBoundary
      v-else-if="error"
      error-title="Không thể tải phim"
      :error-message="error"
      @retry="fetchMovieDetail"
    />

    <!-- No Data State -->
    <div v-else-if="!movieData" class="flex justify-center items-center h-screen text-gray-400">
      <p class="text-xl font-semibold">Không tìm thấy thông tin phim hoặc không tải được dữ liệu.</p>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Hero Section with Backdrop -->
      <div class="relative w-full h-[70vh] min-h-[500px]">
        <!-- Backdrop Image -->
        <div class="absolute inset-0">
          <LazyImage
            :src="movieData.backdrop_path || movieData.poster_path || 'https://placehold.co/1920x1080/1f2937/fbbf24?text=No+Image'"
            :alt="movieData.title"
            image-class="w-full h-full object-cover"
            error-text="Không tải được ảnh nền"
          />
          <!-- Gradient Overlays -->
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
          <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-transparent to-transparent"></div>
        </div>

        <!-- Video Player (when playing) -->
        <div v-if="isPlaying && currentEpisodeLink" class="absolute inset-0 z-10 bg-black">
          <iframe
            :src="currentEpisodeLink"
            :title="`Xem ${movieData.title} ${currentEpisodeName}`"
            class="w-full h-full"
            frameborder="0"
            allowfullscreen
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          ></iframe>
          <button
            @click="stopPlaying"
            class="absolute top-4 right-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2 shadow-lg z-20"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
            </svg>
            Đóng
          </button>
        </div>

        <!-- Content Overlay -->
        <div v-if="!isPlaying" class="absolute inset-0 flex items-end">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 w-full">
            <div class="max-w-2xl">
              <!-- Title -->
              <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-3 drop-shadow-lg">
                {{ movieData.title }}
              </h1>
              
              <!-- Meta Info -->
              <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="text-green-400 font-semibold text-lg">{{ movieData.year }}</span>
                <span class="text-white">•</span>
                <span class="px-3 py-1 border-2 border-gray-400 text-white text-sm font-semibold rounded">
                  {{ movieData.quality || 'HD' }}
                </span>
                <span class="text-white">•</span>
                <span class="text-white">{{ movieData.episode_current || 'Full' }}</span>
                <span class="text-white">•</span>
                <div class="flex items-center gap-1">
                  <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span class="text-white font-semibold">{{ movieData.vote_average || 'N/A' }}</span>
                </div>
              </div>

              <!-- Description -->
              <p class="text-gray-200 text-base md:text-lg leading-relaxed mb-6 line-clamp-3">
                {{ movieData.overview }}
              </p>

              <!-- Action Buttons -->
              <div class="flex flex-wrap gap-3">
                <button
                  @click="handleWatch"
                  class="bg-white hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-md transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-yellow-500/50 text-lg transform hover:scale-105"
                >
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                  </svg>
                  Phát
                </button>
                <button
                  @click="isTrailerDialogOpen = true"
                  class="bg-gray-600/80 hover:bg-gray-500/80 backdrop-blur-sm text-white font-semibold py-3 px-6 rounded-md transition-all duration-300 flex items-center gap-2 hover:scale-105"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Thông tin
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Details Section -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Breadcrumb -->
        <Breadcrumb :items="breadcrumbItems" />

        <!-- Movie Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column - Main Info -->
          <div class="lg:col-span-2 space-y-6">
            <!-- About Section -->
            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl border border-gray-700">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Giới thiệu
              </h2>
              <p class="text-gray-300 leading-relaxed">{{ movieData.overview }}</p>
            </div>

            <!-- Cast & Crew -->
            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl border border-gray-700">
              <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Diễn viên & Đạo diễn
              </h2>
              <div class="space-y-3">
                <div v-if="movieData.actor && movieData.actor.length">
                  <span class="text-gray-400 font-semibold">Diễn viên:</span>
                  <p class="text-white mt-1">{{ movieData.actor.join(', ') }}</p>
                </div>
                <div v-if="movieData.director && movieData.director.length">
                  <span class="text-gray-400 font-semibold">Đạo diễn:</span>
                  <p class="text-white mt-1">{{ movieData.director.join(', ') }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Quick Info -->
          <div>
            <!-- Movie Stats Card -->
            <div class="bg-gradient-to-br from-yellow-500/20 to-orange-500/20 backdrop-blur-sm p-6 rounded-xl border border-yellow-500/30 h-full flex flex-col">
              <h3 class="text-xl font-bold text-yellow-400 mb-4">Thông tin</h3>
              <div class="space-y-3 text-sm flex-1 flex flex-col justify-center">
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  <div>
                    <span class="text-gray-400">Thể loại:</span>
                    <p class="text-white font-semibold">{{ movieData.genres && movieData.genres.length > 0 ? movieData.genres.join(', ') : 'N/A' }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <span class="text-gray-400">Thời lượng:</span>
                    <p class="text-white font-semibold">{{ movieData.runtime || 'N/A' }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <span class="text-gray-400">Quốc gia:</span>
                    <p class="text-white font-semibold">{{ movieData.country && movieData.country.length > 0 ? movieData.country.join(', ') : 'N/A' }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <span class="text-gray-400">Chất lượng:</span>
                    <p class="text-white font-semibold">{{ movieData.quality || 'HD' }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                  </svg>
                  <div>
                    <span class="text-gray-400">Ngôn ngữ:</span>
                    <p class="text-white font-semibold">{{ movieData.lang || 'Vietsub' }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <div>
                    <span class="text-gray-400">Tình trạng:</span>
                    <p class="text-white font-semibold">{{ movieData.status || 'Đang cập nhật' }}</p>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </div>

        <!-- Episodes Section -->
        <div v-if="episodes.length > 0" class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
              <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Tập phim
            </h2>
            <span class="text-gray-400 text-sm">{{ episodes.length }} tập</span>
          </div>
          
          <div class="space-y-3 max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-yellow-500 scrollbar-track-gray-700 pr-2">
            <div
              v-for="(ep, index) in episodes"
              :key="index"
              @click="playEpisode(ep)"
              :class="{
                'border-l-4 border-yellow-500 bg-gray-700/50': currentEpisodeSlug === ep.slug,
                'border-l-4 border-transparent': currentEpisodeSlug !== ep.slug
              }"
              class="bg-gray-800/50 hover:bg-gray-700/50 rounded-lg overflow-hidden cursor-pointer transition-all duration-300 group"
            >
              <div class="flex gap-4 p-4">
                <!-- Episode Number -->
                <div class="flex-shrink-0 w-12 flex items-center justify-center">
                  <span 
                    class="text-2xl font-bold transition-colors"
                    :class="currentEpisodeSlug === ep.slug ? 'text-yellow-400' : 'text-gray-500 group-hover:text-white'"
                  >
                    {{ index + 1 }}
                  </span>
                </div>

                <!-- Episode Thumbnail -->
                <div class="flex-shrink-0 w-36 h-20 bg-gray-700 rounded-lg overflow-hidden relative">
                  <LazyImage
                    :src="movieData.backdrop_path || movieData.poster_path || 'https://placehold.co/160x90/374151/fbbf24?text=' + (index + 1)"
                    :alt="`${ep.name} thumbnail`"
                    image-class="w-full h-full object-cover"
                    container-class="w-full h-full"
                  />
                  <!-- Play Icon Overlay -->
                  <div class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-black ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                      </svg>
                    </div>
                  </div>
                </div>

                <!-- Episode Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 
                      class="font-bold text-base transition-colors"
                      :class="currentEpisodeSlug === ep.slug ? 'text-yellow-400' : 'text-white group-hover:text-yellow-400'"
                    >
                      {{ ep.name }}
                    </h3>
                    <span class="text-xs text-gray-400 whitespace-nowrap">
                      {{ movieData.runtime || '56 phút' }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-400 line-clamp-2 leading-relaxed">
                    {{ movieData.overview ? movieData.overview.substring(0, 120) + '...' : 'Xem ngay để khám phá nội dung hấp dẫn của tập phim này.' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Movies -->
        <div v-if="relatedMovies.length > 0" class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
          <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
            </svg>
            Phim liên quan
          </h2>
          <div v-if="loadingRelated" class="text-center text-gray-400 py-8">
            <svg class="animate-spin h-8 w-8 text-yellow-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Đang tải...
          </div>
          <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <MovieCardRecommended
              v-for="relatedMovie in relatedMovies"
              :key="relatedMovie.id"
              :movie="relatedMovie"
              :trending="false"
            />
          </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
          <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Bình luận
          </h2>

          <!-- Comment Form -->
          <CommentForm 
            :movie-slug="filmSlug"
            @comment-added="fetchComments"
            class="mb-6"
          />

          <!-- Comments List -->
          <div v-if="loadingComments" class="space-y-4">
            <LoadingSkeleton type="list" :count="3" />
          </div>

          <div v-else-if="comments.length > 0" class="space-y-4">
            <div
              v-for="comment in comments"
              :key="comment.id"
              class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 hover:border-gray-600 transition-colors"
            >
              <div class="flex gap-3">
                <img
                  :src="comment.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.full_name || 'User')}&background=f59e0b&color=000`"
                  :alt="comment.full_name"
                  class="w-10 h-10 rounded-full flex-shrink-0"
                />
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-white font-semibold">{{ comment.full_name || comment.username }}</span>
                    <span class="text-gray-500 text-xs">{{ comment.time_ago || formatDate(comment.created_at) }}</span>
                  </div>
                  <p class="text-gray-300 leading-relaxed">{{ comment.content }}</p>
                  <div class="flex items-center gap-4 mt-2">
                    <button class="text-gray-400 hover:text-yellow-400 text-sm flex items-center gap-1 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                      </svg>
                      <span>{{ comment.likes || 0 }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-12 bg-gray-800/30 rounded-lg border border-gray-700">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="text-gray-400">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
          </div>
        </div>
      </div>

      <!-- Trailer Dialog -->
      <div
        v-if="isTrailerDialogOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
        role="dialog"
        aria-labelledby="trailer-title"
      >
        <div class="bg-gray-900 text-white rounded-xl shadow-2xl p-8 w-full max-w-4xl relative">
          <button
            @click="isTrailerDialogOpen = false"
            class="absolute top-4 right-4 text-white text-3xl hover:text-yellow-400 transition-colors"
            aria-label="Đóng trailer"
          >
            ×
          </button>
          <h2 id="trailer-title" class="text-2xl font-semibold mb-4">Trailer: {{ movieData.title }}</h2>
          <div v-if="embedUrl" class="aspect-video w-full">
            <iframe
              :src="embedUrl"
              :title="`Trailer - ${movieData.title}`"
              class="w-full h-full rounded-lg"
              frameborder="0"
              allowfullscreen
              loading="lazy"
            ></iframe>
          </div>
          <p v-else class="text-gray-400 text-center">Không có trailer.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { supabase } from '../supabaseClient'; 
import MovieCardRecommended from '@/shared/MovieCardRecommended.vue';
import CommentForm from '@/components/CommentForm.vue';
import LazyImage from '@/components/LazyImage.vue';
import LoadingSkeleton from '@/components/LoadingSkeleton.vue';
import ErrorBoundary from '@/components/ErrorBoundary.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
import axios from 'axios';
import { API_CONFIG } from '@/config/api';
import { useSEO, generateMovieMeta } from '@/composables/useSEO';

// Khởi tạo router và route
const route = useRoute();
const router = useRouter();

// State reactive
const movieData = ref(null);
const loading = ref(true);
const error = ref(null);
const isTrailerDialogOpen = ref(false);
const filmSlug = ref(route.params.filmName); 
const relatedMovies = ref([]); 
const loadingRelated = ref(false);

// Comments states
const comments = ref([]);
const loadingComments = ref(false);

// Video player states
const isPlaying = ref(false);
const currentEpisodeLink = ref(null);
const currentEpisodeName = ref('');
const currentEpisodeSlug = ref('');
const episodes = ref([]);

// Breadcrumb items (computed based on movie data)
const breadcrumbItems = computed(() => {
  const items = [
    { label: 'Trang chủ', to: '/home' }
  ];
  
  if (movieData.value) {
    // Add movie type (Phim Lẻ or Phim Bộ)
    if (movieData.value.type === 'single') {
      items.push({ label: 'Phim Lẻ', to: '/list/phim-le/page/1' });
    } else if (movieData.value.type === 'series') {
      items.push({ label: 'Phim Bộ', to: '/list/phim-bo/page/1' });
    }
    
    // Add current movie
    items.push({ label: movieData.value.title, to: `/film/${filmSlug.value}` });
  }
  
  return items;
}); 

// Chuyển YouTube URL sang embed
const convertYoutubeUrlToEmbed = (url) => {
  if (!url) return null;
  const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]+)/);
  return match ? `https://www.youtube.com/embed/${match[1]}` : null;
};

// URL embed cho trailer
const embedUrl = ref(null);

// Fetch chi tiết phim từ API phimapi.com
const fetchMovieDetail = async () => {
  loading.value = true;
  error.value = null;
  movieData.value = null; 
  episodes.value = [];
  console.log('moviedetail.vue: Bắt đầu fetchMovieDetail. filmSlug:', filmSlug.value);

  try {
    if (!filmSlug.value) {
      console.warn('moviedetail.vue: filmSlug không có giá trị, không thể fetch.');
      error.value = 'URL phim không hợp lệ.';
      loading.value = false;
      return;
    }

    // Gọi API trực tiếp để lấy cả episodes
    const axios = (await import('axios')).default;
    const response = await axios.get(`https://phimapi.com/phim/${filmSlug.value}`);
    const movie = response.data.movie;
    const episodesData = response.data.episodes || [];

    console.log('moviedetail.vue: API response:', response.data);

    if (movie) {
      // Map data từ API
      movieData.value = {
        id: movie._id,
        title: movie.name,
        original_title: movie.origin_name,
        slug: movie.slug,
        overview: movie.content,
        poster_path: movie.poster_url,
        backdrop_path: movie.thumb_url,
        release_date: movie.year ? `${movie.year}-01-01` : null,
        vote_average: movie.tmdb?.vote_average || 0,
        vote_count: movie.tmdb?.vote_count || 0,
        runtime: movie.time,
        genres: movie.category?.map(c => c.name) || [],
        country: movie.country?.map(c => c.name) || [],
        actor: movie.actor || [],
        director: movie.director || [],
        quality: movie.quality,
        lang: movie.lang,
        episode_current: movie.episode_current,
        episode_total: movie.episode_total,
        trailer_url: movie.trailer_url,
        type: movie.type,
        year: movie.year,
        status: movie.status,
        chieurap: movie.chieurap
      };
      
      // Lấy episodes từ server đầu tiên
      if (episodesData.length > 0 && episodesData[0].server_data) {
        episodes.value = episodesData[0].server_data;
      }
      
      embedUrl.value = convertYoutubeUrlToEmbed(movieData.value.trailer_url);
      console.log('moviedetail.vue: Đã tải thành công dữ liệu phim:', movieData.value);
      console.log('moviedetail.vue: Episodes:', episodes.value);
      
      // Update SEO meta tags
      const movieMeta = generateMovieMeta({
        name: movieData.value.title,
        origin_name: movieData.value.original_title,
        content: movieData.value.overview,
        quality: movieData.value.quality,
        category: movieData.value.genres.map(g => ({ name: g })),
        poster_url: movieData.value.poster_path,
        thumb_url: movieData.value.backdrop_path,
        slug: movieData.value.slug
      });
      const { updateMeta, setMovieStructuredData } = useSEO(movieMeta);
      updateMeta();
      setMovieStructuredData({
        name: movieData.value.title,
        origin_name: movieData.value.original_title,
        content: movieData.value.overview,
        poster_url: movieData.value.poster_path,
        thumb_url: movieData.value.backdrop_path,
        year: movieData.value.year,
        category: movieData.value.genres.map(g => ({ name: g })),
        quality: movieData.value.quality,
        lang: movieData.value.lang,
        rating: movieData.value.vote_average
      });
      
      // Fetch phim liên quan và comments
      if (movieData.value.genres && movieData.value.genres.length > 0) {
        fetchRelatedMovies(movieData.value.genres, movieData.value.id);
      }
      fetchComments();
    } else {
      error.value = 'Không tìm thấy thông tin phim với slug này.'; 
      console.warn('moviedetail.vue: API không trả về dữ liệu phim.');
    }
  } catch (err) {
    console.error('moviedetail.vue: Lỗi không xác định khi fetch chi tiết phim:', err);
    error.value = 'Không thể tải thông tin phim. Vui lòng thử lại sau.';
  } finally {
    loading.value = false;
    console.log('moviedetail.vue: Kết thúc fetchMovieDetail. Loading:', loading.value, 'Error:', error.value);
  }
};

// Fetch phim liên quan
const fetchRelatedMovies = async (genres, currentMovieId) => {
  loadingRelated.value = true;
  relatedMovies.value = [];
  try {
    const { data, error: supabaseError } = await supabase
      .from('movies')
      .select('*')
      .overlaps('genres', genres) 
      .not('id', 'eq', currentMovieId) 
      .order('vote_average', { ascending: false }) 
      .limit(4); 

    if (supabaseError) {
      console.error('Lỗi khi fetch phim liên quan:', supabaseError);
    } else {
      relatedMovies.value = data || [];
      console.log('moviedetail.vue: Phim liên quan đã tải:', relatedMovies.value);
    }
  } catch (err) {
    console.error('Lỗi không xác định khi fetch phim liên quan:', err);
  } finally {
    loadingRelated.value = false;
  }
};

// Fetch comments
const fetchComments = async () => {
  if (!filmSlug.value) return;
  
  loadingComments.value = true;
  try {
    const response = await axios.get(`${API_CONFIG.BACKEND_URL}/comments.php`, {
      params: {
        type: 'movie',
        movie_slug: filmSlug.value,
        limit: 20
      }
    });
    
    if (response.data.success) {
      comments.value = response.data.comments || [];
      console.log('✅ Comments loaded:', comments.value.length);
    }
  } catch (err) {
    console.error('❌ Error fetching comments:', err);
    comments.value = [];
  } finally {
    loadingComments.value = false;
  }
};

// Format date helper
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = Math.floor((now - date) / 1000);
  
  if (diff < 60) return 'Vừa xong';
  if (diff < 3600) return `${Math.floor(diff / 60)} phút trước`;
  if (diff < 86400) return `${Math.floor(diff / 3600)} giờ trước`;
  if (diff < 604800) return `${Math.floor(diff / 86400)} ngày trước`;
  
  return date.toLocaleDateString('vi-VN');
};

// Theo dõi thay đổi của tham số 'filmName' trên route
watch(
  () => route.params.filmName, 
  (newFilmName) => {
    filmSlug.value = newFilmName; 
    console.log('moviedetail.vue: route.params.filmName đã thay đổi thành:', newFilmName);
    if (filmSlug.value) {
      fetchMovieDetail();
    }
  },
  { immediate: true } 
);

// Xử lý nút "Xem phim" - Play tập đầu tiên hoặc Full
const handleWatch = () => {
  if (!movieData.value || episodes.value.length === 0) return;
  
  // Lấy tập đầu tiên
  const firstEpisode = episodes.value[0];
  playEpisode(firstEpisode);
  
  // Scroll to video player
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Play một tập cụ thể
const playEpisode = (episode) => {
  if (!episode || !episode.link_embed) return;
  
  currentEpisodeLink.value = episode.link_embed;
  currentEpisodeName.value = episode.name;
  currentEpisodeSlug.value = episode.slug;
  isPlaying.value = true;
  
  // Scroll to video player
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Dừng phát
const stopPlaying = () => {
  isPlaying.value = false;
  currentEpisodeLink.value = null;
  currentEpisodeName.value = '';
  currentEpisodeSlug.value = '';
};

// Lấy số tập từ tên (ví dụ: "Tập 1" -> "1", "Full" -> "Full")
const getEpisodeNumber = (episodeName) => {
  if (!episodeName) return '';
  if (episodeName.toLowerCase().includes('full')) return 'Full';
  const match = episodeName.match(/\d+/);
  return match ? match[0] : episodeName;
};
</script>

<style scoped>
/* Scrollbar tùy chỉnh */
.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: #eab308 #374151;
}

.scrollbar-thin::-webkit-scrollbar {
  width: 8px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: #374151;
  border-radius: 4px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #eab308;
  border-radius: 4px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #f59e0b;
}

/* Responsive */
@media (max-width: 1024px) {
  .max-w-md {
    max-width: 300px;
  }
  .w-64 {
    width: 200px;
  }
}

@media (max-width: 640px) {
  .text-3xl {
    font-size: 1.75rem;
  }
  .text-2xl {
    font-size: 1.5rem;
  }
  .text-lg {
    font-size: 1.25rem;
  }
  .max-w-md {
    max-width: 250px;
  }
  .w-64 {
    width: 150px;
  }
}
</style>
