import React, { useCallback, useEffect, useState } from "react";
import Loop from "./loop";
import Search from "./search";
import Pagination from "./pager";
import apiFetch from "@wordpress/api-fetch";
import { addQueryArgs } from "@wordpress/url";

const fetchItems = (queryParams = {}, method = "GET") => {
	apiFetch.use(apiFetch.createRootURLMiddleware(window.codesoup_ilc.root));

	const path = Object.keys(queryParams).length
		? addQueryArgs("/instapage-cache/v1/pages", queryParams)
		: "/instapage-cache/v1/pages";

	return apiFetch({
		path: path,
		method: method,
		credentials: "same-origin",
		headers: {
			"Content-Type": "application/json",
			"Accept": "application/json",
			"X-WP-Nonce": window.codesoup_ilc.nonce,
		},
	});
};

const InstapageCacheManager = () => {
	const [items, setItems] = useState([]);
	const [selectedItem, setSelectedItem] = useState(null);
	const [args, setArgs] = useState({
		page: 1,
		per_page: 15,
		search: "",
	});

	const updateItems = useCallback(() => {
		fetchItems(args).then((res) => {
			setItems(res);
		});
	}, [args]);

	useEffect(() => {
		updateItems();
	}, [updateItems]);

	const handleSearch = (searchString) => {
		setArgs({ ...args, search: searchString, page: 1 });
	};

	const handlePageSelect = (pageNumber) => {
		setArgs({ ...args, page: pageNumber }); // Reset to page 1 on new search
	};

	/**
	 * Delete one or more cached pages
	 */
	const handleClick = (event, anchorElement) => {
		event.preventDefault();
		onCacheDelete(anchorElement.getAttribute("data-item-slug")).then(() => {
			updateItems();
		});
	};

	const onCacheDelete = (itemSlug) => {
		apiFetch.use(
			apiFetch.createRootURLMiddleware(window.codesoup_ilc.root),
		);

		return apiFetch({
			path: addQueryArgs("/instapage-cache/v1/delete", {
				slug: itemSlug,
			}),
			method: "POST",
			credentials: "same-origin",
			headers: {
				"Content-Type": "application/json",
				"Accept": "application/json",
				"X-WP-Nonce": window.codesoup_ilc.nonce,
			},
		});
	};

	return (
		<div className="wrap">
			<h1 className="wp-heading-inline">Instapage Cache Manager</h1>
			<hr className="wp-header-end" />
			<Search onSearch={handleSearch} />
			<table className="wp-list-table widefat striped table-view-list posts">
				<thead>
					<tr>
						<th>ID</th>
						<th>Page URL</th>
						<th>Published On</th>
						<th>Cached</th>
						<th className="td-right">
							<button
								className="button button-primary"
								data-item-slug="all"
								onClick={(event) =>
									handleClick(event, event.currentTarget)
								}
							>
								Regenerate All
							</button>
						</th>
					</tr>
				</thead>
				<Loop
					items={items}
					onPageDelete={handleClick}
				/>
			</table>

			<Pagination
				args={args}
				posts={items}
				onPageSelect={handlePageSelect}
			/>
		</div>
	);
};

export default InstapageCacheManager;
