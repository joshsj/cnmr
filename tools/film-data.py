import sys
import requests
import csv
import calendar


def main():
    URL = f"http://www.omdbapi.com/?apikey={sys.argv[1]}"
    IDs = sys.argv[2:]  # IMDB film ids

    # newline prevents empty lines between rows
    with open("films.csv", mode="a", newline="") as f, open("genres.csv", mode="a", newline="") as g:
        wFilm = csv.writer(f)  # automatic formatting
        wGenre = csv.writer(g)

        for ID in IDs:
            # get film data
            res = requests.get(
                URL,
                params={
                    "i": ID,
                    # uncomment for longer plots (can get very long)
                    # "plot": "full",
                }
            )

            data = res.json()

            if data["Response"] == "True":  # check film found
                genres = data["Genre"].split(", ")

                # save film genres
                for g in genres:
                    wGenre.writerow([
                        "NULL",  # primary key
                        g        # genre
                    ])

                released = data["Released"].split()[::-1]  # YYYY mon DD

                # replace abbreviation with num
                released[1] = str(list(calendar.month_abbr).index(released[1]))
                released = "-".join(released)  # join with -

                film = [
                    "NULL",                      # primary key
                    data["Title"],               # title
                    data["Plot"],                # description
                    released,                    # release date
                    data["Runtime"].split()[0],  # return as 122 mins
                    data["Rated"],               # age certification
                    "9.00",                      # default prices
                    "6.50",
                    "6.50",
                    "7.50"
                ]

                wFilm.writerow(film)

                print(f"Wrote film {data['Title']}")
            else:
                print(f"Film not found: {ID}")


if __name__ == "__main__":
    main()
