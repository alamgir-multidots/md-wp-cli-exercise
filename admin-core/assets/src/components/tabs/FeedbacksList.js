import { React, useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

function NftSoldProducts() {
	const [ getFeedbacks, setFeedbacks ] =  useState({});

    useEffect( () => {
		apiFetch( {
			url: md_wp_cli_exercise_settings.feedbacks_api_url,
			method: 'GET',
		} ).then( (response) => {
			setFeedbacks( response );
		} );
	}, [] );

	return (
		<>
            <div className="pr-8 pb-8 pt-8 pl-8">
                <h3 class="text-2xl pb-4">{__('Feedbacks List', 'md-wp-cli-exercise')}</h3>
                <div className="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table className="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    &nbsp;
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {__( 'Name', 'md-wp-cli-exercise' )}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {__( 'Phone', 'md-wp-cli-exercise' )}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {__( 'Email', 'md-wp-cli-exercise' )}
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    {__( 'Feedbacks', 'md-wp-cli-exercise' )}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        { Object.keys(getFeedbacks).length > 0 ? (
                            <>
                                { getFeedbacks.map( ( feedbacks, key ) => (
                                    <tr className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {key+1}
                                        </th>
                                        <td className="px-6 py-4">
                                            {feedbacks.first_name} {feedbacks.last_name}
                                        </td>
                                        <td className="px-6 py-4">
                                            {feedbacks.phone}
                                        </td>
                                        <td className="px-6 py-4">
                                            {feedbacks.email}
                                        </td>
                                        <td className="px-6 py-4">
                                            {feedbacks.feedback}
                                        </td>
                                    </tr>
                                ) ) }
                            </>
                        ) : (
                            <tr className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td className="px-6 py-4 text-center" colSpan='6'>
                                    { __( 'Data Not Found!', 'md-wp-cli-exercise' ) }
                                </td>
                            </tr>
                        ) }
                        </tbody>
                    </table>
                </div>
            </div>
		</>
	);
}

export default NftSoldProducts;