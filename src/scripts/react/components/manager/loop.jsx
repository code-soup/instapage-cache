import React, { useEffect, useState } from "react";

const Loop = ({ items, onPageDelete }) => {
	const [posts, setPosts] = useState([]);
	const [error, setError] = useState(null);

	/**
	 * Set snapshots data
	 */
	useEffect(() => {
		if ("posts" in items) {
			setPosts(items.posts);
		}
	}, [items]);

	const handleClick = (event, anchorElement) => {
		event.preventDefault();
		onPageDelete(event, anchorElement);
	};

	if (error) {
		return (
			<tbody>
				<tr>
					<td colSpan="6">Error: {error.message}</td>
				</tr>
			</tbody>
		);
	}
	if (posts.length === 0) {
		return (
			<tbody>
				<tr>
					<td colSpan="6">No items found</td>
				</tr>
			</tbody>
		);
	}

	return (
		<tbody>
			{posts.map((item, index) => {
				if (null === item.post) return null;

				return (
					<tr key={index}>
						<td>{item.id}</td>
						<td>
							<a
								href={item.enterprise_url}
								target="_blank"
							>
								{item.slug}
							</a>
						</td>
						<td>{item.time}</td>
						<td>{item.cached === 0 ? "No" : "Yes"}</td>
						<td>{item.cache_disabled ? "Disabled" : "Enabled"}</td>
						<td className="td-right">
							<button
								className="button button-primary"
								data-item-id={item.id}
								data-item-slug={item.slug}
								data-action={
									item.cache_disabled ? "enable" : "disable"
								}
								onClick={(event) =>
									handleClick(event, event.currentTarget)
								}
							>
								{item.cache_disabled
									? "Enable Caching"
									: "Disable Caching"}
							</button>
						</td>
						<td className="td-right">
							<button
								disabled={
									item.cached === 0 || item.cache_disabled
								}
								className="button button-primary"
								data-item-slug={item.slug}
								data-action="delete"
								onClick={(event) =>
									handleClick(event, event.currentTarget)
								}
							>
								Regenerate Cache
							</button>
						</td>
					</tr>
				);
			})}
		</tbody>
	);
};

export default Loop;
