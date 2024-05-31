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
						<td className="td-right">
							<button
								disabled={item.cached === 0}
								className="button button-primary"
								data-item-slug={item.slug}
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
