import { __ } from '@wordpress/i18n'
import { useBlockProps, InspectorControls } from '@wordpress/block-editor'
import {
	PanelBody,
	Button,
	ButtonGroup,
	__experimentalNumberControl as NumberControl,
	ToggleControl
} from '@wordpress/components'
import { useSelect } from '@wordpress/data'
import parse from 'html-react-parser'
import { Swiper, SwiperSlide } from 'swiper/react'
import 'swiper/css' // Core Swiper CSS
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import SwiperCore, { Navigation, Pagination } from 'swiper'
// Register modules
SwiperCore.use([Navigation, Pagination])
export default function Edit({ attributes, setAttributes }) {
	const {
		numberOfPosts,
		showPostAuthor,
		linkToAuthorPage,
		showPostDate,
		layout,
		showPostImage
	} = attributes

	const posts = useSelect(select => {
		return select('core').getEntityRecords('postType', 'post', { per_page: numberOfPosts })
	}, [numberOfPosts])

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
	const layouts = [
		{ label: 'grid', value: 'grid', tooltip: 'Grid style layout' },
		{ label: 'list', value: 'list', tooltip: 'List style layout' },
		{ label: 'carousel', value: 'carousel', tooltip: 'Carousel layout' },
	];
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Block settings', 'sp-recent-post-showcase')}>
					<ButtonGroup
						className={`sp-team-button-group sp-team-component-mb`}
					>
						<div className='sp-team-component-top'>
							<label className='sp-team-component-title'>Layout</label>
						</div>
						<div className={`sp-team-button-group-list`}>
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

					<NumberControl
						label={__('Number of posts to display', 'sp-recent-post-showcase')}
						value={numberOfPosts}
						onChange={(value) => setAttributes({ numberOfPosts: parseInt(value) })}
					/>
					<ToggleControl
						label={__('Show post image', 'sp-recent-post-showcase')}
						checked={showPostImage}
						onChange={(value) => setAttributes({ showPostImage: value })}
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
			<div {...useBlockProps()}>
				<div className="styble-post-grid-wrapper">
					{posts && posts.map((post, index) => (
						<div key={post.id} className="styble-post-item">
							{showPostImage && media && media[index] ? (
								<div className="styble-post-image">
									<img src={media[index].source_url} alt={media[index].alt_text} />
								</div>
							) : null}

							<div className="styble-post-body">
								<h2 className="styble-post-title">
									<a href={post.link}>{parse(post.title.rendered)}</a>
								</h2>

								{(showPostAuthor || showPostDate) && (
									<div className="styble-post-meta">
										{showPostAuthor && authors && authors[index] ? (
											<span className="styble-post-author">
												{__('By', 'sp-recent-post-showcase')} {linkToAuthorPage ? (
													<a href={authors[index][0].link}>{authors[index][0].name}</a>
												) : (
													<span>{authors[index][0].name}</span>
												)}
											</span>
										) : null}

										{showPostDate ? (
											<time
												className="styble-post-date"
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

								<div className="styble-post-content" dangerouslySetInnerHTML={{ __html: post.excerpt.rendered }} />

								<div className="styble-post-btn">
									<a className="styble-post-read-more" href={post.link}>
										<span>{__('Read More', 'sp-recent-post-showcase')}</span>
									</a>
								</div>
							</div>
						</div>

					))}
				</div>
				{/* <Swiper
					spaceBetween={30}
					slidesPerView={3}
					navigation={true}
					pagination={{ clickable: true }}
					breakpoints={{
						640: { slidesPerView: 1 },
						768: { slidesPerView: 2 },
						1024: { slidesPerView: 3 }
					}}
					autoPlay={true}
				>
					{posts && posts.map((post, index) => (
						<SwiperSlide key={post.id}>
							<div className="styble-post-item">
								{showPostImage && media && media[index] ? (
									<div className="styble-post-image">
										<img src={media[index].source_url} alt={media[index].alt_text} />
									</div>
								) : null}

								<div className="styble-post-body">
									<h2 className="styble-post-title">
										<a href={post.link}>{parse(post.title.rendered)}</a>
									</h2>

									{(showPostAuthor || showPostDate) && (
										<div className="styble-post-meta">
											{showPostAuthor && authors && authors[index] ? (
												<span className="styble-post-author">
													{__('By', 'sp-recent-post-showcase')} {linkToAuthorPage ? (
														<a href={authors[index][0].link}>{authors[index][0].name}</a>
													) : (
														<span>{authors[index][0].name}</span>
													)}
												</span>
											) : null}

											{showPostDate ? (
												<time
													className="styble-post-date"
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

									<div className="styble-post-content" dangerouslySetInnerHTML={{ __html: post.excerpt.rendered }} />

									<div className="styble-post-btn">
										<a className="styble-post-read-more" href={post.link}>
											<span>{__('Read More', 'sp-recent-post-showcase')}</span>
										</a>
									</div>
								</div>
							</div>
						</SwiperSlide>
					))}
				</Swiper> */}
			</div>
		</>
	);
}
