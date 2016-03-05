<select name="category" id="inputCategory">
  @if (Auth::user() && Auth::user()->hasAnyRole(['admin', 'user']))
  <option value="notice" {{ old('category', $category) == 'notice' ? 'selected' : '' }}>공지사항</option>
  @endif
  <option value="free" {{ old('category', $category) == 'free' ? 'selected' : '' }}>자유게시판</option>
  <option value="tuts" {{ old('category', $category) == 'tuts' ? 'selected' : '' }}>Laravel 강좌게시판</option>
  <option value="tips" {{ old('category', $category) == 'tips' ? 'selected' : '' }}>Laravel 팁게시판</option>
  <option value="help" {{ old('category', $category) == 'help' ? 'selected' : '' }}>Laravel 질문게시판</option>
  <option value="packages" {{ old('category', $category) == 'packages' ? 'selected' : '' }}>Laravel 패키지</option>
  <option value="sites" {{ old('category', $category) == 'sites' ? 'selected' : '' }}>Laravel 사이트 소개</option>
  <option value="jobs" {{ old('category', $category) == 'jobs' ? 'selected' : '' }}>구인구직</option>
</select>