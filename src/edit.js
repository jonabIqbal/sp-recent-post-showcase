import { __ } from '@wordpress/i18n'
import { useBlockProps, InspectorControls } from '@wordpress/block-editor'
import {
	PanelBody,
	Button,
	ButtonGroup,
	__experimentalNumberControl as NumberControl,
	ToggleControl,
	RangeControl,
	SelectControl
} from '@wordpress/components'
import { useEffect, useRef, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data'
import parse from 'html-react-parser'
import { Swiper, SwiperSlide } from 'swiper/react'
import {
	Navigation,
	Pagination,
	Grid,
	Autoplay,
	Scrollbar,
	Keyboard,
	Mousewheel,
	EffectFade,
	EffectCoverflow,
	EffectFlip,
	EffectCube,
	FreeMode,
} from 'swiper/modules';
import 'swiper/css' // Core Swiper CSS
import 'swiper/css/bundle'
// Register modules
export default function Edit({ attributes, setAttributes, clientId, isSelected }) {
	const {
		numberOfPosts,
		showPostAuthor,
		linkToAuthorPage,
		showPostDate,
		layout,
		gridColumn,
		selectedCategory,
		selectedPostType,
		showPostContent,
		uniqueId,
		showPostImage
	} = attributes
	const swiperDotsRef = useRef(null);
	const swiperNavNextRef = useRef(null);
	const swiperNavPrevRef = useRef(null);
	const categories = useSelect(select =>
		select('core').getEntityRecords('taxonomy', 'category', { per_page: -1 }),
		[]
	);
	const blockProps = useBlockProps();
	const posts = useSelect(select => {
		return select('core').getEntityRecords('postType', selectedPostType, {
			per_page: numberOfPosts,
			categories: selectedCategory || undefined,
		})
	}, [numberOfPosts, selectedCategory, selectedPostType]);

	const media = useSelect(select => {
		if (!posts) {
			return []
		}
		return posts.map(post => {
			if (!post.featured_media) {
				return null
			}
			return select('core').getMedia(post.featured_media)
		})
	}, [posts])

	const authors = useSelect(select => {
		if (!posts || !showPostAuthor) {
			return []
		}
		return posts.map(post => {
			if (!post.author) {
				return null
			}
			return select('core').getUsers({ who: 'authors', include: [post.author] })
		})
	}, [posts, showPostAuthor])
	//  All layout object.
	const layouts = [
		{ label: 'Grid', value: 'grid', tooltip: 'Grid style layout' },
		{ label: 'List', value: 'list', tooltip: 'List style layout' },
		{ label: 'Carousel', value: 'carousel', tooltip: 'Carousel layout' },
	];
	const postTypes = useSelect(select => {
		return select('core').getPostTypes({ per_page: -1 })?.filter(
			(type) =>
				type.viewable &&
				type.slug !== 'attachment' &&
				type.slug !== 'wp_block'
		);
	}, []);
	if (!uniqueId) {
		setAttributes({ uniqueId: clientId });
	}
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'sp-recent-post-showcase')}>
					<SelectControl
						label={__('Select Post Type', 'sp-recent-post-showcase')}
						value={selectedPostType}
						options={
							(postTypes || []).map(postType => ({
								label: postType.labels.singular_name,
								value: postType.slug,
							}))
						}
						onChange={(value) => setAttributes({ selectedPostType: value })}
					/>


					<ButtonGroup
						className={`sp-post-button-group sp-post-component-mb`}
					>
						<div className='sp-post-component-top'>
							<label className='sp-post-component-title'>Layout</label>
						</div>
						<div className={`sp-post-button-group-list`}>
							{
								layouts.map((item, i) => (
									<Button className={(layout == item.value) ? 'active' : ''} key={i} value={item.value} onClick={(e) => {
										const selectedLayout = e.target.closest('button').value;
										setAttributes({ layout: selectedLayout });
									}}><span>{item.label}</span></Button>
								))
							}
						</div>
					</ButtonGroup>
					<RangeControl
						label={__('Columns', 'team-member-profile')}
						value={gridColumn}
						onChange={(value) => setAttributes({ gridColumn: value })}
						min={1}
						max={6}
					/>
					<NumberControl
						label={__('Number of posts to display', 'sp-recent-post-showcase')}
						value={numberOfPosts}
						onChange={(value) => setAttributes({ numberOfPosts: parseInt(value) })}
					/>
					<SelectControl
						label={__('Filter by Category', 'sp-recent-post-showcase')}
						value={selectedCategory}
						options={[
							{ label: __('All Categories', 'sp-recent-post-showcase'), value: 0 },
							...(categories || []).map(cat => ({
								label: cat.name,
								value: cat.id,
							})),
						]}
						onChange={(value) => setAttributes({ selectedCategory: parseInt(value) })}
					/>
					<ToggleControl
						label={__('Show post image', 'sp-recent-post-showcase')}
						checked={showPostImage}
						onChange={(value) => setAttributes({ showPostImage: value })}
					/>
					<ToggleControl
						label={__('Show post Content', 'sp-recent-post-showcase')}
						checked={showPostContent}
						onChange={(value) => setAttributes({ showPostContent: value })}
					/>
					<ToggleControl
						label={__('Show post author', 'sp-recent-post-showcase')}
						checked={showPostAuthor}
						onChange={(value) => setAttributes({ showPostAuthor: value })}
					/>
					{showPostAuthor ? (
						<ToggleControl
							label={__('Link to author page', 'sp-recent-post-showcase')}
							checked={linkToAuthorPage}
							onChange={(value) => setAttributes({ linkToAuthorPage: value })}
						/>
					) : null}
					<ToggleControl
						label={__('Show post date', 'sp-recent-post-showcase')}
						checked={showPostDate}
						onChange={(value) => setAttributes({ showPostDate: value })}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{layout !== 'carousel' && (
					<div className="grid-wrapper-parent">
						<div className={`sp-post-grid-wrapper ${layout}-layout`} style={{ gridTemplateColumns: `repeat(${gridColumn}, 1fr)` }}>
							{posts && posts.map((post, index) => (
								<div key={post.id} className="sp-post-item">
									{showPostImage && media && media[index] ? (
										<div className="sp-post-image">
											<img src={media[index].source_url} alt={media[index].alt_text} />
										</div>
									) : null}

									<div className="sp-post-body">
										<h2 className="sp-post-title">
											<a href={post.link}>{parse(post.title.rendered)}</a>
										</h2>

										{(showPostAuthor || showPostDate) && (
											<div className="sp-post-meta">
												{showPostAuthor && authors && authors[index] ? (
													<span className="sp-post-author">
														{__('By', 'sp-recent-post-showcase')} {linkToAuthorPage ? (
															<a href={authors[index][0].link}>{authors[index][0].name}</a>
														) : (
															<span>{authors[index][0].name}</span>
														)}
													</span>
												) : null}

												{showPostDate ? (
													<time
														className="sp-post-date"
														dateTime={post.date}
													>
														{__(' On', 'sp-recent-post-showcase')} {new Date(post.date).toLocaleDateString(undefined, {
															year: 'numeric',
															month: 'long',
															day: 'numeric',
														})}
													</time>
												) : null}
											</div>
										)}


										{showPostContent ? (<div className="sp-post-content" dangerouslySetInnerHTML={{ __html: post.excerpt.rendered }} />): null}

										<div className="sp-post-btn">
											<a className="sp-post-read-more" href={post.link}>
												<span>{__('Read More', 'sp-recent-post-showcase')}</span>
											</a>
										</div>
									</div>
								</div>
							))}
						</div>
						{(posts) && <div className='sp-post-pagination'>
							<a className='current' href='#'><span>1</span></a>
							<a href='#'><span>2</span></a>
							<a href='#'><span>Next</span></a>
						</div>}
					</div>
				)}
				{layout == 'carousel' && (
					<Swiper
						spaceBetween={30}
						slidesPerView={gridColumn}
						pagination={{ clickable: true }}
						breakpoints={{
							640: { slidesPerView: 1 },
							768: { slidesPerView: 2 },
							1024: { slidesPerView: gridColumn }
						}}
						autoplay={{
							delay: 2000,
							disableOnInteraction: false,
							pauseOnMouseEnter: true,
						}}
						speed={600}
						loop={true}
						navigation={{
							nextEl: swiperNavNextRef.current,
							prevEl: swiperNavPrevRef.current,
							enabled: true,
						}}
						modules={[
							Navigation,
							Pagination,
							Scrollbar,
							Grid,
							Autoplay,
							Keyboard,
							Mousewheel,
							EffectFade,
							EffectCoverflow,
							EffectFlip,
							EffectCube,
							FreeMode,
						]} // Include Autoplay in modules
					>
						{posts && posts.map((post, index) => (
							<SwiperSlide key={post.id}>
								<div className="sp-post-item">
									{showPostImage && media && media[index] ? (
										<div className="sp-post-image">
											<img src={media[index].source_url} alt={media[index].alt_text} />
										</div>
									) : null}
									<div className="sp-post-body">
										<h2 className="sp-post-title">
											<a href={post.link}>{parse(post.title.rendered)}</a>
										</h2>

										{(showPostAuthor || showPostDate) && (
											<div className="sp-post-meta">
												{showPostAuthor && authors && authors[index] ? (
													<span className="sp-post-author">
														{__('By', 'sp-recent-post-showcase')} {linkToAuthorPage ? (
															<a href={authors[index][0].link}>{authors[index][0].name}</a>
														) : (
															<span>{authors[index][0].name}</span>
														)}
													</span>
												) : null}

												{showPostDate ? (
													<time
														className="sp-post-date"
														dateTime={post.date}
													>
														{__('On', 'sp-recent-post-showcase')} {new Date(post.date).toLocaleDateString(undefined, {
															year: 'numeric',
															month: 'long',
															day: 'numeric',
														})}
													</time>
												) : null}
											</div>
										)}

										{showPostContent ? (<div className="sp-post-content" dangerouslySetInnerHTML={{ __html: post.excerpt.rendered }} />) : null}

										<div className="sp-post-btn">
											<a className="sp-post-read-more" href={post.link}>
												<span>{__('Read More', 'sp-recent-post-showcase')}</span>
											</a>
										</div>
									</div>
								</div>
							</SwiperSlide>
						))}
						<div className="swiper-button-prev" ref={swiperNavPrevRef} ></div>
						<div className="swiper-button-next" ref={swiperNavNextRef}></div>
					</Swiper>)}
			</div >
		</>
	);
}
